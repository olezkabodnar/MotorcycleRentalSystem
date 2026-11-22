<?php

namespace Tests\Unit\Event;

use App\Event\Listener;

// test double that remembers every event it was given
class SpyListener implements Listener
{
    public $handled = [];

    public function handle($event)
    {
        $this->handled[] = $event;
    }
}
