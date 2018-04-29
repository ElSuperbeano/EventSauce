<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Serialization;

class ConstructingEventSerializer implements EventSerializer
{
    /**
     * @param SerializableEvent $event
     *
     * @return array
     */
    public function serializeEvent(SerializableEvent $event): array
    {
        return $event->toPayload();
    }

    public function unserializePayload(string $className, array $payload): SerializableEvent
    {
        /* @var SerializableEvent $className */
        return $className::fromPayload($payload);
    }
}
