<?php

namespace App\Event;

// Observer: keeps the list of listeners and notifies them
class EventDispatcher
{
    private $listeners = [];

    public function subscribe($eventClass, $listener)
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch($event)
    {
        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return;
        }

        foreach ($this->listeners[$eventClass] as $listener) {
            $listener->handle($event);
        }
    }
}
