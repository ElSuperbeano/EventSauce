<?php

declare(strict_types=1);

namespace EventSauce\EventSourcing\Integration\TestingAggregates;

use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\Time\Clock;

class DummyCommandHandler
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    /**
     * @var Clock
     */
    private $clock;

    public function __construct(AggregateRootRepository $repository, Clock $clock)
    {
        $this->repository = $repository;
        $this->clock = $clock;
    }

    /**
     * @param DummyCommand $command
     *
     * @throws DummyException
     */
    public function handle($command)
    {
        /** @var DummyAggregate $aggregate */
        $aggregate = $this->repository->retrieve($command->aggregateRootId());

        try {
            if ($command instanceof DummyCommand) {
                $aggregate->performDummyTask();
            } elseif ($command instanceof IgnoredCommand) {
                $aggregate->dontDoAnything();
            } elseif ($command instanceof ExceptionInducingCommand) {
                $aggregate->throwAnException();
            } elseif ($command instanceof DummyIncrementCommand) {
                $aggregate->increment();
            }
        } finally {
            $this->repository->persist($aggregate);
        }
    }
}
