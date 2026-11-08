<?php
//https://dev.to/xxzeroxx/php-design-patterns-front-controller-jjo


$routes = [
    //Customer controller routes
    'GET /customers' => ['controller' => 'CustomerController', 'action' => 'getCustomers'],
    'GET /customers/{id}' => ['controller' => 'CustomerController', 'action' => 'getCustomerById'],
    'POST /customers' => ['controller' => 'CustomerController', 'action' => 'createCustomer'],
    'PUT /customers/{id}' => ['controller' => 'CustomerController', 'action' => 'updateCustomer'],
    'DELETE /customers/{id}' => ['controller' => 'CustomerController', 'action' => 'deleteCustomer'],
];
