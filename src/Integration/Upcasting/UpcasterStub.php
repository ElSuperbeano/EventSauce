<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Integration\Upcasting;

use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Upcasting\DelegatableUpcaster;


class UpcasterStub implements DelegatableUpcaster
{
    public function canUpcast(string $type, array $payload): bool
    {
        $version = $payload['headers']['version'] ?? 0;

        return $this->type() === $type && $version < 1;
    }

    public function upcast(array $payload): array
    {
        $payload['payload']['property'] = 'upcasted';
        $payload['headers']['version'] = 1;

        return $payload;
    }

    public function type(): string
    {
        return (new DotSeparatedSnakeCaseInflector())->classNameToType(UpcastedEventStub::class);
    }
}
