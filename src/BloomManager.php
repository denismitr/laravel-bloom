<?php


namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\Bloom;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterImplementation;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

final class BloomManager
{
    /**
     * @var array
     */
    private $config;

    /**
     * BloomManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $key
     * @return Bloom
     */
    public function key(string $key): Bloom
    {
        $implementation = $this->getBloomImplementation($key);
        $hasher = $this->getHasherImplementation($key);

        $indexer = new Indexer(new $hasher());

        $connection = Redis::connection(
            Arr::get($this->config, 'connection', 'default')
        );

        return new $implementation($key, $this->config['default'], $indexer, $connection);
    }

    /**
     * @param string $key
     * @return string
     */
    private function getBloomImplementation(string $key): string
    {
        $implementation = Arr::get($this->config, 'implementation');

        if ( ! is_a($implementation, Bloom::class) ) {
            InvalidBloomFilterImplementation::because(
                get_class($implementation) . ' does not implement ' . Bloom::class . ' interface'
            );
        }

        return $implementation;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getHasherImplementation(string $key): string
    {
        $hasher = Arr::get($this->config, 'hasher');

        if ( ! is_a($hasher, Bloom::class) ) {
            InvalidBloomFilterImplementation::because(
                get_class($hasher) . ' does not implement ' . Bloom::class . ' interface'
            );
        }

        return $hasher;
    }
}