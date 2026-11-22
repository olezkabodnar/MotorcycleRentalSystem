<?php

namespace App\Event;

// Observer: everything that reacts to an event
interface Listener
{
    public function handle($event);
}
