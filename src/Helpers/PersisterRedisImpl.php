<?php


namespace Denismitr\Bloom\Helpers;


use Denismitr\Bloom\Contracts\Persister;
use Illuminate\Contracts\Redis\Connection;

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

    public function setBit(string $key, int $index, bool $value): void
    {
        $this->redis->command("setbit", [$key, $index, 1]);
    }

    public function getBit(string $key, int $index): bool
    {
        return !! $this->redis->command("getbit", [$key, $index]);
    }

}