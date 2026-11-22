<?php

namespace App\Mail;

// Factory Method: the Product the listeners create
interface EmailMessage
{
    public function to();

    public function subject();

    public function body();
}
