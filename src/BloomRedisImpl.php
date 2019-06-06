<?php

namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\Bloom;
use Denismitr\Bloom\Contracts\Hasher;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Support\Arr;

class BloomRedisImpl implements Bloom
{
    /**
     * @var Connection
     */
    private $redis;

    /**
     * @var Hasher
     */
    private $indexer;

    /**
     * @var integer
     */
    private $numHashes;

    /**
     * @var mixed
     */
    private $size;
    /**
     * @var string
     */
    private $key;

    /**
     * BloomRedisImpl constructor.
     * @param string $key
     * @param array $config
     * @param Indexer $indexer
     * @param Connection $redis
     */
    public function __construct(string $key, array $config, Indexer $indexer, Connection $redis)
    {
        $this->redis = $redis;
        $this->indexer = $indexer;
        $this->numHashes = Arr::get($config, 'num_hashes', 3);
        $this->size = Arr::get($config, 'size', 100);
        $this->key = $key;
    }

    /**
     * @param mixed $item
     */
    public function add($item): void
    {
        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

        $indexes->each(function(int $index) {
            $this->redis->command("setbit", [$index, 1]);
        });
    }

    public function test($item): bool
    {
        // TODO: Implement test() method.
    }
}