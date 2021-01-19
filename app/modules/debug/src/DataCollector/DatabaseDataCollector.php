<?php

namespace GreenCheap\Debug\DataCollector;

use DebugBar\Bridge\DoctrineCollector;
use Doctrine\DBAL\Logging\DebugStack;
use GreenCheap\Database\Connection;

class DatabaseDataCollector extends DoctrineCollector
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param DebugStack $debugStack
     */
    public function __construct(Connection $connection, DebugStack $debugStack = null)
    {
        $this->connection = $connection;
        $this->debugStack = $debugStack;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $driver = $this->connection->getDriver()->getName();

        return array_replace(compact('driver'), parent::collect());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database';
    }
}
