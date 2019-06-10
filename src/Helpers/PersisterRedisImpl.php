<?php


namespace Denismitr\Bloom\Helpers;


use Denismitr\Bloom\Contracts\Persister;
use Illuminate\Redis\Connections\Connection;

final class PersisterRedisImpl implements Persister
{
    /**
     * @var Connection
     */
    private $redis;

    /**
     * PersisterRedisImpl constructor.
     * @param Connection $redis
     */
    public function __construct(Connection $redis)
    {
        $this->redis = $redis;
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
    public function reset(string $key): void
    {
        $this->redis->del([$key]);
    }
}