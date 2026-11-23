<?php

namespace App\Controller;

use App\Exception\ValidationException;
use DateTimeImmutable;

// MVC Controller for reservation pages
class ReservationController
{
    private $reservationService;
    private $customerService;
    private $motorcycles;
    private $extras;
    private $view;

    public function __construct($reservationService, $customerService, $motorcycles, $extras, $view)
    {
        $this->reservationService = $reservationService;
        $this->customerService = $customerService;
        $this->motorcycles = $motorcycles;
        $this->extras = $extras;
        $this->view = $view;
    }

    // GET /reservations (was reservations/view_reservations.php)
    public function getReservations()
    {
        $reservations = $this->reservationService->listReservations();

        $created = null;
        if (isset($_GET['created'])) {
            $created = (int) $_GET['created'];
        }

        $this->view->render('reservations/index', ['reservations' => $reservations, 'created' => $created]);
    }

    // GET /reservations/new (was the form part of reservations/create_reservation.php)
    public function newReservation()
    {
        $this->showForm([], null, null);
    }

    // POST /reservations/preview
    public function previewReservation()
    {
        try {
            $price = $this->reservationService->preview(
                $this->input('motorcycle'),
                $this->dateInput('start_date'),
                $this->dateInput('end_date'),
                $this->chosenExtras()
            );
            $this->showForm($_POST, $price, null);
        } catch (ValidationException $e) {
            $this->showForm($_POST, null, $e->getMessage());
        }
    }

    // POST /reservations
    public function createReservation()
    {
        try {
            $id = $this->reservationService->create(
                $this->input('customer'),
                $this->input('motorcycle'),
                $this->dateInput('start_date'),
                $this->dateInput('end_date'),
                $this->chosenExtras()
            );
        } catch (ValidationException $e) {
            $this->showForm($_POST, null, $e->getMessage());
            return;
        }

        header('Location: /MotorcycleRentalSystem/public/reservations?created=' . $id);
    }

    private function showForm($old, $price, $error)
    {
        $this->view->render('reservations/create', [
            'customers' => $this->customerService->listCustomers(),
            'motorcycles' => $this->motorcycles->findAll(),
            'extras' => $this->extras->findAll(),
            'old' => $old,
            'price' => $price,
            'error' => $error,
        ]);
    }

    private function input($name)
    {
        if (!isset($_POST[$name])) {
            return '';
        }
        return trim($_POST[$name]);
    }

    private function dateInput($name)
    {
        $value = $this->input($name);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        // PHP turns 2027-02-30 into 2027-03-02, so the date must come back unchanged
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new ValidationException('Please choose a valid start date and end date.');
        }
        return $date;
    }

    private function chosenExtras()
    {
        $ids = [];
        if (isset($_POST['extras']) && is_array($_POST['extras'])) {
            $ids = $_POST['extras'];
        }
        return $this->extras->findByIds($ids);
    }
}
