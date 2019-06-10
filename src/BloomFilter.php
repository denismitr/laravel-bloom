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
     * @var int
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
        $this->numHashes = Arr::get($config, 'num_hashes', 5);
        $this->size = Arr::get($config, 'size', 10000000);
        $this->key = $key;
        $this->persister = $persister;
    }

    /**
     * @param string|integer|float $item
     */
    public function add($item): void
    {
        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

        $this->persister->setBits($this->key, $indexes);
    }

    /**
     * @param string|integer|float $item
     * @return bool
     */
    public function test($item): bool
    {
        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

        return $this->persister->getBits($this->key, $indexes)->test();
    }

    public function reset(): void
    {
        $this->persister->reset($this->key);
    }

    /**
     * @return int
     */
    public function getNumHashes(): int
    {
        return $this->numHashes;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}