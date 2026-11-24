<?php

namespace App\Service;

use App\Event\ReservationCancelled;
use App\Event\ReservationCreated;
use App\Exception\ValidationException;
use App\Pricing\BasePrice;
use App\Pricing\FlatExtra;
use App\Pricing\PerDayExtra;
use DateTimeImmutable;

// Service Layer: business rules for reservations (FR3, FR4, FR5, FR6)
class ReservationService
{
    private $reservations;
    private $customers;
    private $pricingFactory;
    private $dispatcher;

    public function __construct($reservations, $customers, $pricingFactory, $dispatcher)
    {
        $this->reservations = $reservations;
        $this->customers = $customers;
        $this->pricingFactory = $pricingFactory;
        $this->dispatcher = $dispatcher;
    }

    public function listReservations()
    {
        return $this->reservations->findAll();
    }

    // REFACTORED: Long Method - price was calculated inside the 280-line create_reservation.php
    public function quote($rate, $start, $end, $extras)
    {
        $start = $this->dateOnly($start);
        $end = $this->dateOnly($end);

        // Simple Factory picks the Strategy, BasePrice is its Context
        $strategy = $this->pricingFactory->create($start, $end);
        $price = new BasePrice($strategy, $rate, $start, $end);

        // Decorator: wrap the price once for every extra
        $days = $start->diff($end)->days;
        foreach ($extras as $extra) {
            if ($extra->chargeType == 'per_day') {
                $price = new PerDayExtra($price, $extra, $days);
            } else {
                $price = new FlatExtra($price, $extra, $days);
            }
        }

        return $price;
    }

    public function preview($regNo, $start, $end, $extras)
    {
        $start = $this->dateOnly($start);
        $end = $this->dateOnly($end);
        $motorcycle = $this->checkBooking($regNo, $start, $end);

        return $this->quote($motorcycle['Rate'], $start, $end, $extras);
    }

    // REFACTORED: Long Method - validation, SQL and HTML were all mixed in create_reservation.php
    public function create($custNo, $regNo, $start, $end, $extras)
    {
        $start = $this->dateOnly($start);
        $end = $this->dateOnly($end);
        $motorcycle = $this->checkBooking($regNo, $start, $end);

        $customer = $this->customers->findById($custNo);
        if (!$customer) {
            throw new ValidationException('Customer not found.');
        }

        $price = $this->quote($motorcycle['Rate'], $start, $end, $extras);

        $row = [
            'RegNo' => $regNo,
            'MBcode' => $motorcycle['MBcode'],
            'CustNo' => $custNo,
            'StartDate' => $start->format('Y-m-d'),
            'EndDate' => $end->format('Y-m-d'),
            'Price' => $price->total(),
            'Status' => 'A',
        ];
        $id = $this->reservations->insert($row, $extras);

        // Observer: the service only announces it, listeners send the email
        $this->dispatcher->dispatch(new ReservationCreated(
            $id,
            $customer->fullName(),
            $customer->email,
            $regNo,
            $row['StartDate'],
            $row['EndDate'],
            $row['Price'],
            $motorcycle['Make'] . ' ' . $motorcycle['Model']
        ));

        return $id;
    }

    // FR5: cancel is a soft delete, the row stays with status 'C'
    public function cancel($id)
    {
        $reservation = $this->reservations->findById($id);
        if (!$reservation) {
            throw new ValidationException('Reservation not found.');
        }
        if ($reservation['Status'] == 'C') {
            throw new ValidationException('This reservation is already cancelled.');
        }

        $this->reservations->cancel($id);

        // old data can point to a missing customer, then there is nobody to email
        $customer = $this->customers->findById($reservation['CustNo']);
        if ($customer) {
            $this->dispatcher->dispatch(new ReservationCancelled(
                $id,
                $customer->fullName(),
                $customer->email,
                $reservation['RegNo']
            ));
        }
    }

    private function checkBooking($regNo, $start, $end)
    {
        $today = new DateTimeImmutable('today');

        if ($end <= $start) {
            throw new ValidationException('End date must be after start date.');
        }
        if ($start < $today) {
            throw new ValidationException('Start date cannot be in the past.');
        }

        $motorcycle = $this->reservations->findMotorcycle($regNo);
        if (!$motorcycle) {
            throw new ValidationException('Motorcycle not found.');
        }

        if (!$this->reservations->isAvailable($regNo, $start, $end)) {
            throw new ValidationException('This motorcycle is not available for the selected dates.');
        }

        return $motorcycle;
    }

    // compare dates only: the legacy code used new DateTime() with the time, so "start today" was rejected
    private function dateOnly($date)
    {
        return new DateTimeImmutable($date->format('Y-m-d'));
    }
}
