<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Integration\SynchronousDispatching;

use EventSauce\EventSourcing\EventStub;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use PHPUnit\Framework\TestCase;

class SynchronousMessageDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function dispatching_messages_synchronously()
    {
        $stubconsumer = new SynchronousConsumerStub();
        $syncDispatcher = new SynchronousMessageDispatcher($stubconsumer, $stubconsumer);
        $message = new Message(new EventStub('value'));
        $syncDispatcher->dispatch($message, $message);
        $this->assertEquals([$message, $message, $message, $message], $stubconsumer->handled);
    }

    /**
     * @test
     */
    public function dispatching_using_a_chain()
    {
        $stubconsumer = new SynchronousConsumerStub();
        $syncDispatcher = new SynchronousMessageDispatcher($stubconsumer);
        $dispatcherChain = new MessageDispatcherChain($syncDispatcher, $syncDispatcher);
        $message = new Message(new EventStub('value'));
        $dispatcherChain->dispatch($message);
        $this->assertEquals([$message, $message], $stubconsumer->handled);
    }
}
