<?php


namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\{Persister};
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistenceDriver;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Factories\HasherFactory;
use Denismitr\Bloom\Factories\PersisterFactory;
use Denismitr\Bloom\Helpers\Indexer;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;

/**
 * Class BloomManager
 * @package Denismitr\Bloom
 */
final class BloomManager
{
    /**
     * @var array
     */
    private $config;

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
        $bloomConfig = $config->get('bloom');

        if (
            ! is_array($bloomConfig)
            || empty($bloomConfig)
            || ! isset($bloomConfig['default'])
            || ! isset($bloomConfig['keys'])
        ) {
            throw InvalidBloomFilterConfiguration::because("Bloom filter configuration file [bloom.php] is empty, invalid or misplaced");
        }

        $this->config = $bloomConfig;
        $this->persisterFactory = $persisterFactory;
        $this->hasherFactory = $hasherFactory;
    }

    /**
     * @param string $key
     * @param string|null $keySuffix
     * @return BloomFilter
     * @throws Exceptions\InvalidBloomFilterHashFunctionsNumber
     * @throws Exceptions\InvalidBloomFilterSize
     * @throws UnsupportedBloomFilterPersistenceDriver
     * @throws UnsupportedHashingAlgorithm
     */
    public function key(string $key, ?string $keySuffix = null): BloomFilter
    {
        $config = $this->resolveKeySpecificConfig($key);

        $indexer = $this->resolveIndexer($config);
        $persister = $this->resolvePersister($config);

        return $this->resolveBloomFilter($key, $keySuffix, $indexer, $persister);
    }

    /**
     * @param string $key
     * @param string|null $keySuffix
     * @param Indexer $indexer
     * @param Persister $persister
     * @return BloomFilter
     * @throws Exceptions\InvalidBloomFilterHashFunctionsNumber
     * @throws Exceptions\InvalidBloomFilterSize
     */
    private function resolveBloomFilter(
        string $key,
        ?string $keySuffix,
        Indexer $indexer,
        Persister $persister
    ): BloomFilter
    {
        $config = $this->resolveKeySpecificConfig($key);

        $key = $keySuffix ? $key.strval($keySuffix) : $key;

        return new BloomFilter($key, $config, $indexer, $persister);
    }

    /**
     * @param array $config
     * @return Indexer
     * @throws UnsupportedHashingAlgorithm
     */
    private function resolveIndexer(array $config): Indexer
    {
        $algorithm = Arr::get($config, 'hashing_algorithm');

        $hasher = $this->hasherFactory->make($algorithm);

        return new Indexer($hasher);
    }

    /**
     * @param array $config
     * @return Persister
     * @throws UnsupportedBloomFilterPersistenceDriver
     */
    private function resolvePersister(array $config): Persister
    {
        $driver = Arr::get($config, 'persistence.driver');
        $connection = Arr::get($config, 'persistence.connection', 'default');

        return $this->persisterFactory->make($driver, $connection);
    }

    /**
     * @param string $key
     * @return array
     */
    private function resolveKeySpecificConfig(string $key): array
    {
        return Arr::get($this->config, "keys.{$key}", $this->config['default']);
    }
}