<?php

declare(strict_types=1);

namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\Hasher;
use Denismitr\Bloom\Contracts\Persister;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterHashFunctionsNumber;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterSize;
use Denismitr\Bloom\Exceptions\InvalidItemType;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Support\Arr;

final class BloomFilter
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
     *
     * @param string $key
     * @param array $config
     * @param Indexer $indexer
     * @param Persister $persister
     * @throws InvalidBloomFilterSize
     * @throws InvalidBloomFilterHashFunctionsNumber
     */
    public function __construct(string $key, array $config, Indexer $indexer, Persister $persister)
    {
        $this->indexer = $indexer;
        $this->key = $key;
        $this->persister = $persister;

        $this->numHashes = $this->validatedNumHashes(Arr::get($config, 'num_hashes', 5));
        $this->size = $this->validatedSize(Arr::get($config, 'size', 10000000));
    }

    /**
     * @param string|integer|float $item
     * @throws InvalidItemType
     */
    public function add($item): void
    {
        $this->verifyItem($item);

        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

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

        $indexes = $this->indexer->getIndexes($this->numHashes, strval($item), $this->size);

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
        return $this->numHashes;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return int|string
     * @throws InvalidBloomFilterSize
     */
    private function validatedSize($size): int
    {
        if ( ! is_integer($size) || $size <= $this->numHashes) {
            throw InvalidBloomFilterSize::size($size);
        }

        if ($size > $this->persister->getMaxCapacity()) {
            throw InvalidBloomFilterSize::max($size, $this->persister->getMaxCapacity());
        }

        return intval($size);
    }

    /**
     * @param $num
     * @return int|string
     * @throws InvalidBloomFilterHashFunctionsNumber
     */
    private function validatedNumHashes($num): int
    {
        if ( ! is_integer($num) || $num <= 0) {
            throw InvalidBloomFilterHashFunctionsNumber::number($num);
        }

        return intval($num);
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