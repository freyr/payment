<?php

declare(strict_types=1);

namespace Freyr\Bus;

use ReflectionClass;
use RuntimeException;

class CommandBus
{
    private array $handlers = [];

    public function register(callable $callable): void
    {
        $this->handlers[] = $callable;
    }

    public function run(object $command): void
    {
        foreach ($this->handlers as $handler) {
            if ($this->canHandle($handler, $command)) {
                $this->execute($handler, $command);
                return;
            }
        }

        throw new RuntimeException("No handler found for command: " . get_class($command));
    }

    private function canHandle(object $handler, object $command): bool
    {
        $reflection = new ReflectionClass($handler);
        $method = $reflection->getMethod('__invoke');

        $parameters = $method->getParameters();
        if (count($parameters) !== 1) {
            return false; // Handlers must have exactly one parameter
        }

        return $parameters[0]->getType()->getName() === get_class($command);
    }

    protected function execute(callable  $handler, object $command): void
    {
        $handler($command);
    }
}