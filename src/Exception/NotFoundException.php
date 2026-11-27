<?php

namespace App\Exception;

use Exception;

// no route or no controller method for the URL; shown as a 404 page
class NotFoundException extends Exception
{
}
