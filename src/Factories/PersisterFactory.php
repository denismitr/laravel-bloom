<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Factories;


use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistence;
use Denismitr\Bloom\Helpers\PersisterRedisImpl;
use Illuminate\Support\Facades\Redis;

class PersisterFactory
{
    const REDIS_DRIVER = 'redis';

    /**
     * @param string $driver
     * @param string $connection
     * @param int $capacity
     * @return Persister
     * @throws InvalidBloomFilterConfiguration
     * @throws UnsupportedBloomFilterPersistence
     */
    public function make(string $driver, string $connection, int $capacity): Persister
    {
        try {
            $conn= Redis::connection($connection);
        } catch (\InvalidArgumentException $e) {
            throw InvalidBloomFilterConfiguration::because($e->getMessage());
        }

        switch(strtolower($driver)) {
            case self::REDIS_DRIVER:
                return new PersisterRedisImpl($conn, $capacity);
            default:
                throw UnsupportedBloomFilterPersistence::driver($driver);
        }
    }
}