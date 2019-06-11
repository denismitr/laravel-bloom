<?php


namespace Denismitr\Bloom\Factories;


use Denismitr\Bloom\Contracts\Persister;
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
     */
    public function make($driver, $connection): Persister
    {
        if ( ! is_string($driver) ) {
            throw UnsupportedBloomFilterPersistenceDriver::type( gettype($driver) );
        }

        $conn= Redis::connection($connection);

        switch(strtolower($driver)) {
            case self::REDIS_DRIVER:
                return new PersisterRedisImpl($conn);
            default:
                throw UnsupportedBloomFilterPersistenceDriver::driver($driver);
        }
    }
}