<?php


namespace Denismitr\Bloom\Factories;


use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistenceDriver;
use Denismitr\Bloom\Helpers\PersisterRedisImpl;
use Illuminate\Support\Facades\Redis;

class PersisterFactory
{
    const REDIS_DRIVER = 'redis';

    /**
     * @param string $driver
     * @param string $connection
     * @return Persister
     * @throws UnsupportedBloomFilterPersistenceDriver
     * @throws InvalidBloomFilterConfiguration
     */
    public function make($driver, $connection): Persister
    {
        if ( ! is_string($driver) ) {
            throw UnsupportedBloomFilterPersistenceDriver::type( gettype($driver) );
        }

        try {
            $conn= Redis::connection($connection);
        } catch (\InvalidArgumentException $e) {
            throw InvalidBloomFilterConfiguration::because($e->getMessage());
        }

        switch(strtolower($driver)) {
            case self::REDIS_DRIVER:
                return new PersisterRedisImpl($conn);
            default:
                throw UnsupportedBloomFilterPersistenceDriver::driver($driver);
        }
    }
}