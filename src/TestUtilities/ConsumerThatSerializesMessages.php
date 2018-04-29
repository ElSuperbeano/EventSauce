<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\TestUtilities;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

use PHPUnit\Framework\TestCase;
use stdClass;

class ConsumerThatSerializesMessages implements Consumer
{
    /**
     * @var MessageSerializer
     */
    private $serializer;

    public function __construct(MessageSerializer $serializer = null)
    {
        $this->serializer = $serializer ?: new ConstructingMessageSerializer();
    }

    public function handle(Message $message)
    {
        $payload = $this->serializer->serializeMessage($message);
        $deserializedMessage = $this->serializer->unserializePayload($payload)
            ?? new Message(new stdClass());
        TestCase::assertEquals($message->event(), $deserializedMessage->event());
    }
}
