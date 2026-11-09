<?php

namespace App\Kernel;

class View
{
    public function render($template, $data = [])
    {
        include __DIR__ . '/../View/layout.php'; 
    }
}
