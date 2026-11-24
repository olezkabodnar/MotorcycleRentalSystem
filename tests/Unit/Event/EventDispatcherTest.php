<?php

namespace Tests\Unit\Event;

use App\Event\EventDispatcher;
use App\Event\ReservationCancelled;
use App\Event\ReservationCreated;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testOnlyListenersOfThatEventAreCalled()
    {
        $dispatcher = new EventDispatcher();
        $createdListener = new SpyListener();
        $cancelledListener = new SpyListener();
        $dispatcher->subscribe(ReservationCreated::class, $createdListener);
        $dispatcher->subscribe(ReservationCancelled::class, $cancelledListener);

        $event = new ReservationCreated(12, 'Test Customer', 'test@example.com', '25-KE-7788', '2026-11-23', '2026-12-01', 405.00, 'Yamaha MT-07');
        $dispatcher->dispatch($event);

        $this->assertSame([$event], $createdListener->handled);
        $this->assertCount(0, $cancelledListener->handled);
    }

    public function testEveryListenerOfTheEventIsCalled()
    {
        $dispatcher = new EventDispatcher();
        $first = new SpyListener();
        $second = new SpyListener();
        $dispatcher->subscribe(ReservationCreated::class, $first);
        $dispatcher->subscribe(ReservationCreated::class, $second);

        $event = new ReservationCreated(12, 'Test Customer', 'test@example.com', '25-KE-7788', '2026-11-23', '2026-12-01', 405.00, 'Yamaha MT-07');
        $dispatcher->dispatch($event);

        // one event, many observers
        $this->assertSame([$event], $first->handled);
        $this->assertSame([$event], $second->handled);
    }

    public function testEventWithNoListenersDoesNothing()
    {
        $this->expectNotToPerformAssertions();
        $dispatcher = new EventDispatcher();

        // passes if no error, e.g. no "undefined array key"
        $dispatcher->dispatch(new ReservationCancelled(12, 'Test Customer', 'test@example.com', '25-KE-7788'));
    }
}
