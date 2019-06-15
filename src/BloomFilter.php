<?php

declare(strict_types=1);

namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\Hasher;
use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Exceptions\InvalidItemType;
use Denismitr\Bloom\Config\KeySpecificConfig;
use Denismitr\Bloom\Helpers\Indexer;

final class BloomFilter
{
    /**
     * @var Hasher
     */
    private $indexer;

    /**
     * @var Persister
     */
    private $persister;

    /**
     * @var KeySpecificConfig
     */
    private $config;
    /**
     * @var string
     */
    private $key;

    /**
     * BloomRedisImpl constructor.
     *
     * @param string $key
     * @param KeySpecificConfig $config
     * @param Indexer $indexer
     * @param Persister $persister
     */
    public function __construct(string $key, KeySpecificConfig $config, Indexer $indexer, Persister $persister)
    {
        $this->config = $config;
        $this->indexer = $indexer;
        $this->persister = $persister;
        $this->key = $key;
    }

    /**
     * @param string|integer|float $item
     * @throws InvalidItemType
     */
    public function add($item): void
    {
        $this->verifyItem($item);

        $indexes = $this->indexer->getIndexes(
            $this->config->getNumHashes(),
            strval($item),
            $this->config->getSize()
        );

        $this->persister->setBits($this->key, $indexes);
    }

    /**
     * @param string|integer|float $item
     * @return bool
     * @throws InvalidItemType
     */
    public function test($item): bool
    {
        $this->verifyItem($item);

        $indexes = $this->indexer->getIndexes(
            $this->config->getNumHashes(),
            strval($item),
            $this->config->getSize()
        );

        return $this->persister->getBits($this->key, $indexes)->test();
    }

    public function clear(): void
    {
        $this->persister->clear($this->key);
    }

    /**
     * @return int
     */
    public function getNumHashes(): int
    {
        return $this->config->getNumHashes();
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->config->getSize();
    }

    /**
     * @param $item
     * @throws InvalidItemType
     */
    private function verifyItem($item): void
    {
        if ( ! is_numeric($item) && ! is_string($item) ) {
            throw InvalidItemType::item($item);
        }
    }
}