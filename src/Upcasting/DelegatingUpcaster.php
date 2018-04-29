<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Upcasting;

use EventSauce\EventSourcing\Header;
use Generator;

final class DelegatingUpcaster implements Upcaster
{
    /**
     * @var DelegatableUpcaster[][]
     */
    private $upcasters;

    public function __construct(DelegatableUpcaster ...$upcasters)
    {
        foreach ($upcasters as $upcaster) {
            $this->upcasters[$upcaster->type()][] = $upcaster;
        }
    }

    public function upcaster(array $message): ?Upcaster
    {
        $type = $message['headers'][Header::EVENT_TYPE];

        foreach ($this->upcasters[$type] ?? [] as $upcaster) {
            if ($upcaster->canUpcast($type, $message)) {
                return $upcaster;
            }
        }

        return null;
    }

    public function upcast(array $message): array
    {
        if ($upcaster = $this->upcaster($message)) {
            $message = $upcaster->upcast($message);
        }
        return $message;
    }

    public function canUpcast(string $type, array $message): bool
    {
        return isset($this->upcasters[$type]);
    }
}
