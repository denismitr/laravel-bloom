<?php

namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\Hasher;
use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Support\Arr;

class BloomFilter
{
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
     * @var Persister
     */
    private $persister;

    /**
     * BloomRedisImpl constructor.
     * @param string $key
     * @param array $config
     * @param Indexer $indexer
     * @param Persister $persister
     */
    public function __construct(string $key, array $config, Indexer $indexer, Persister $persister)
    {
        $this->indexer = $indexer;
        $this->numHashes = Arr::get($config, 'num_hashes', 3);
        $this->size = Arr::get($config, 'size', 100);
        $this->key = $key;
        $this->persister = $persister;
    }

    /**
     * @param string|integer|float $item
     */
    public function add($item): void
    {
        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

        $indexes->each(function(int $index) {
            $this->persister->setBit($this->key, $index, true);
        });
    }

    /**
     * @param string|integer|float $item
     * @return bool
     */
    public function test($item): bool
    {
        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

        foreach ($indexes as $index) {
            if ( true !== $this->persister->getBit($this->key, $index) ) {
                return false;
            }
        }

        return true;
    }
}