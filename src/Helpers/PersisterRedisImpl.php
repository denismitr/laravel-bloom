<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Helpers;


use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterSize;
use Illuminate\Redis\Connections\Connection;

final class PersisterRedisImpl implements Persister
{
    const MAX_CAPACITY = 4294967296;

    /**
     * @var Connection
     */
    private $redis;

    /**
     * @var int
     */
    private $capacity;

    /**
     * PersisterRedisImpl constructor.
     * @param Connection $redis
     * @param int $capacity
     * @throws InvalidBloomFilterSize
     */
    public function __construct(Connection $redis, int $capacity)
    {
        if ($capacity > self::getMaxCapacity()) {
            throw InvalidBloomFilterSize::max($capacity, self::getMaxCapacity());
        }

        $this->redis = $redis;
        $this->capacity = $capacity;
    }

    /**
     * @param string $key
     * @param Indexes $indexes
     */
    public function setBits(string $key, Indexes $indexes): void
    {
        $this->redis->transaction(function ($client) use ($key, $indexes) {
            foreach ($indexes->get() as $index) {
                $client->setbit($key, $index, 1);
            }
        });
    }

    /**
     * @param string $key
     * @param Indexes $indexes
     * @return Bits
     */
    public function getBits(string $key, Indexes $indexes): Bits
    {
        $responses = $this->redis->transaction(function ($client) use ($key, $indexes) {
            foreach ($indexes->get() as $index) {
                $client->getbit($key, $index);
            }
        });

        return new Bits($responses);
    }

    /**
     * @param string $key
     */
    public function clear(string $key): void
    {
        $this->redis->del([$key]);
    }

    /**
     * @return int
     */
    public function getMaxCapacity(): int
    {
        return self::MAX_CAPACITY;
    }
}