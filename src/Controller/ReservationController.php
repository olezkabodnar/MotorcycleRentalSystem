<?php

namespace App\Controller;

// MVC Controller for reservation pages
class ReservationController
{
    private $reservationService;
    private $view;

    public function __construct($reservationService, $view)
    {
        $this->reservationService = $reservationService;
        $this->view = $view;
    }

    // GET /reservations (was reservations/view_reservations.php)
    public function getReservations()
    {
        $reservations = $this->reservationService->listReservations();

        $this->view->render('reservations/index', ['reservations' => $reservations]);
    }
}
