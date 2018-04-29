<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Serialization;

use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\EventStub;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Time\TestClock;
use EventSauce\EventSourcing\UuidAggregateRootId;
use PHPUnit\Framework\TestCase;


class ConstructingMessageSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function serializing_messages_with_aggregate_root_ids()
    {
        $aggregateRootId = UuidAggregateRootId::create();
        $inflector = new DotSeparatedSnakeCaseInflector();
        $aggregateRootIdType = $inflector->instanceToType($aggregateRootId);
        $timeOfRecording = (new TestClock())->pointInTime();
        $message = new Message(new EventStub('original value'), [
            Header::AGGREGATE_ROOT_ID      => $aggregateRootId,
            Header::AGGREGATE_ROOT_ID_TYPE => $aggregateRootIdType,
            Header::TIME_OF_RECORDING      => $timeOfRecording->toString(),
            Header::EVENT_TYPE             => $inflector->classNameToType(EventStub::class),
        ]);
        $serializer = new ConstructingMessageSerializer();
        $serialized = $serializer->serializeMessage($message);
        $deserializedMessage = $serializer->unserializePayload($serialized);
        $messageWithConstructedAggregateRootId = $message->withHeader(Header::AGGREGATE_ROOT_ID, $aggregateRootId);
        $this->assertEquals($messageWithConstructedAggregateRootId, $deserializedMessage);
    }
}
