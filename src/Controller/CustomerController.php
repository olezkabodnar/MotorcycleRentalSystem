<?php

namespace App\Controller;

// MVC Controller for customer pages
class CustomerController
{
    private $customerService;
    private $view;

    public function __construct($customerService, $view)
    {
        $this->customerService = $customerService;
        $this->view = $view;
    }

    // GET /customers (was customers/view_customers.php)
    public function index()
    {
        $customers = $this->customerService->listCustomers();

        return $this->view->render('customers/index', ['customers' => $customers]);
    }
}
