<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

use Freyr\EventSourcing\AggregateChanged;

class AsyncEventEmitterConverter
{

    public function dispatch(array $events)
    {
        /** @var AggregateChanged $event */
        foreach ($events as $event) {
            if (in_array($event->name, self::$publishableEvents)) {
                $integrationEvent = $this->convert($event);
                $this->publisher->publish($integrationEvent);
            }
        }
    }

    private function convert(AggregateChanged $event)
    {

    }
}