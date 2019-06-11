<?php


namespace Denismitr\Bloom;


use Denismitr\Bloom\Contracts\{Hasher, Persister};
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistenceDriver;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Helpers\HasherMD5Impl;
use Denismitr\Bloom\Helpers\Indexer;
use Denismitr\Bloom\Helpers\PersisterRedisImpl;
use Illuminate\Config\Repository;
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
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config->get('bloom');
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
        $hasher = $this->resolveHasher($key);

        $indexer = new Indexer($hasher);

        $persister = $this->resolvePersister($key);

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
     * @param string $key
     * @return Hasher
     * @throws UnsupportedHashingAlgorithm
     */
    private function resolveHasher(string $key): Hasher
    {
        $config = $this->resolveKeySpecificConfig($key);

        $algorithm = Arr::get($config, 'hashing_algorithm');

        switch ($algorithm) {
            case 'md5':
                return new HasherMD5Impl();
            default:
                throw UnsupportedHashingAlgorithm::algorithm(strval($algorithm));
        }
    }

    /**
     * @param string $key
     * @return Persister
     * @throws UnsupportedBloomFilterPersistenceDriver
     */
    private function resolvePersister(string $key): Persister
    {
        $config = $this->resolveKeySpecificConfig($key);

        $driver = Arr::get($config, 'persistence.driver');
        $connection = Arr::get($config, 'persistence.connection');

        switch ($driver) {
            case 'redis':
                $conn= Redis::connection($connection);

                return new PersisterRedisImpl($conn);
            default:
                throw UnsupportedBloomFilterPersistenceDriver::driver($driver);
        }
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