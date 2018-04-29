<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Serialization;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Upcasting\Upcaster;


class UpcastingMessageSerializer implements MessageSerializer
{
    /**
     * @var MessageSerializer
     */
    private $eventSerializer;

    /**
     * @var Upcaster
     */
    private $upcaster;

    public function __construct(MessageSerializer $eventSerializer, Upcaster $upcaster)
    {
        $this->eventSerializer = $eventSerializer;
        $this->upcaster = $upcaster;
    }

    public function serializeMessage(Message $message): array
    {
        return $this->eventSerializer->serializeMessage($message);
    }

    public function unserializePayload(array $payload): Message
    {
        if ($this->upcaster->canUpcast($payload['headers'][Header::EVENT_TYPE], $payload)) {
            $payload = $this->upcaster->upcast($payload);
        }

        return $this->eventSerializer->unserializePayload($payload);
    }
}
