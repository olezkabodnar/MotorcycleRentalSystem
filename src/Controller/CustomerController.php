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
    public function getCustomers()
    {
        $customers = $this->customerService->listCustomers();

           $this->view->render('customers/index', ['customers' => $customers]);
    }
}
