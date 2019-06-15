<?php

declare(strict_types=1);


namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\{Persister};
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistence;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Factories\HasherFactory;
use Denismitr\Bloom\Factories\PersisterFactory;
use Denismitr\Bloom\Config\KeySpecificConfig;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Config\Repository;

/**
 * Class BloomManager
 * @package Denismitr\Bloom
 */
final class BloomManager
{
    /**
     * @var KeySpecificConfig
     */
    private $bloomConfig;

    /**
     * @var PersisterFactory
     */
    private $persisterFactory;

    /**
     * @var HasherFactory
     */
    private $hasherFactory;

    /**
     * BloomManager constructor.
     * @param Repository $config
     * @param PersisterFactory $persisterFactory
     * @param HasherFactory $hasherFactory
     * @throws InvalidBloomFilterConfiguration
     */
    public function __construct(
        Repository $config,
        PersisterFactory $persisterFactory,
        HasherFactory $hasherFactory
    )
    {
        $this->bloomConfig = $config->get('bloom');
        $this->persisterFactory = $persisterFactory;
        $this->hasherFactory = $hasherFactory;
    }

    /**
     * @param string $key
     * @param string|null $keySuffix
     * @return BloomFilter
     * @throws Exceptions\InvalidBloomFilterHashFunctionsNumber
     * @throws Exceptions\InvalidBloomFilterSize
     * @throws UnsupportedBloomFilterPersistence
     * @throws UnsupportedHashingAlgorithm
     * @throws InvalidBloomFilterConfiguration
     */
    public function key(string $key, ?string $keySuffix = null): BloomFilter
    {
        $keySpecificConfig = KeySpecificConfig::of($key, $this->bloomConfig);

        $indexer = $this->resolveIndexer($keySpecificConfig);
        $persister = $this->resolvePersister($keySpecificConfig);

        return $this->resolveBloomFilter($key, $keySuffix, $keySpecificConfig, $indexer, $persister);
    }

    /**
     * @param string $key
     * @param string|null $keySuffix
     * @param KeySpecificConfig $keySpecificConfig
     * @param Indexer $indexer
     * @param Persister $persister
     * @return BloomFilter
     */
    private function resolveBloomFilter(
        string $key,
        ?string $keySuffix,
        KeySpecificConfig $keySpecificConfig,
        Indexer $indexer,
        Persister $persister
    ): BloomFilter
    {
        $key = $keySuffix ? $key.strval($keySuffix) : $key;

        return new BloomFilter($key, $keySpecificConfig, $indexer, $persister);
    }

    /**
     * @param KeySpecificConfig $config
     * @return Indexer
     * @throws UnsupportedHashingAlgorithm
     */
    private function resolveIndexer(KeySpecificConfig $config): Indexer
    {
        return new Indexer(
            $this->hasherFactory->make($config->getHashingAlgorithm())
        );
    }

    /**
     * @param KeySpecificConfig $config
     * @return Persister
     * @throws InvalidBloomFilterConfiguration
     * @throws UnsupportedBloomFilterPersistence
     */
    private function resolvePersister(KeySpecificConfig $config): Persister
    {
        return $this->persisterFactory->make(
            $config->getPersistenceDriver(),
            $config->getPersistenceConnection(),
            $config->getSize()
        );
    }
}