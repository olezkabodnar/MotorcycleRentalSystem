<?php

namespace App\Exception;

use Exception;

// user input errors; safe to show their message on the page
class ValidationException extends Exception
{
}
