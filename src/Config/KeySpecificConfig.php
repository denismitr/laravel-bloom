<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Config;


use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterHashFunctionsNumber;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterSize;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistence;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Illuminate\Support\Arr;

final class KeySpecificConfig
{
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
    private $hashingAlgorithm;

    /**
     * @var string
     */
    private $persistenceDriver;

    /**
     * @var string
     */
    private $persistenceConnection;

    /**
     * @var string
     */
    private $key;

    /**
     * BloomFilterConfig constructor.
     * @param string $key
     * @param int $numHashes
     * @param int $size
     * @param string $hashingAlgorithm
     * @param string $persistenceDriver
     * @param string $persistenceConnection
     */
    private function __construct(
        string $key,
        int $numHashes,
        int $size,
        string $hashingAlgorithm,
        string $persistenceDriver,
        string $persistenceConnection
    )
    {
        $this->key = $key;
        $this->numHashes = $numHashes;
        $this->size = $size;
        $this->hashingAlgorithm = $hashingAlgorithm;
        $this->persistenceDriver = $persistenceDriver;
        $this->persistenceConnection = $persistenceConnection;
    }

    /**
     * @param string $key
     * @param array $bloomConfig
     * @return KeySpecificConfig
     * @throws InvalidBloomFilterConfiguration
     * @throws InvalidBloomFilterHashFunctionsNumber
     * @throws InvalidBloomFilterSize
     */
    public static function of(string $key, $bloomConfig = []): self
    {
        if (
            ! is_array($bloomConfig)
            || empty($bloomConfig)
            || ! isset($bloomConfig['default'])
            || ! isset($bloomConfig['keys'])
        ) {
            throw InvalidBloomFilterConfiguration::because(
                "Bloom filter configuration file [bloom.php] is empty, invalid or misplaced."
            );
        }

        $keySpecificConfig = Arr::get($bloomConfig, "keys.{$key}", $bloomConfig['default']);

        $numHashes = self::validatedNumHashes(Arr::get($keySpecificConfig, 'num_hashes'));

        $size = self::validatedSize(
            Arr::get($keySpecificConfig, 'size'),
            $numHashes
        );

        $hashingAlgorithm = Arr::get($keySpecificConfig, 'hashing_algorithm');
        $persistenceDriver = Arr::get($keySpecificConfig, 'persistence.driver');
        $persistenceConnection = Arr::get($keySpecificConfig, 'persistence.connection');

        if ( ! is_string($hashingAlgorithm) ) {
            throw UnsupportedHashingAlgorithm::algorithm( $persistenceDriver );
        }

        if ( ! is_string($persistenceDriver) ) {
            throw UnsupportedBloomFilterPersistence::driver( $persistenceDriver );
        }

        if ( ! is_string($persistenceConnection) ) {
            throw UnsupportedBloomFilterPersistence::connection( $persistenceConnection );
        }

        return new static($key, $numHashes, $size, $hashingAlgorithm, $persistenceDriver, $persistenceConnection);
    }

    /**
     * @param $size
     * @return int|string
     * @throws InvalidBloomFilterSize
     */
    private static function validatedSize($size, $numHashes): int
    {
        if ( ! is_integer($size) || $size <= $numHashes) {
            throw InvalidBloomFilterSize::size($size);
        }

        $size = intval($size);

        return $size;
    }

    /**
     * @param $num
     * @return int|string
     * @throws InvalidBloomFilterHashFunctionsNumber
     */
    private static function validatedNumHashes($num): int
    {
        if ( ! is_integer($num) || $num <= 0) {
            throw InvalidBloomFilterHashFunctionsNumber::number($num);
        }

        return intval($num);
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
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getHashingAlgorithm(): string
    {
        return $this->hashingAlgorithm;
    }

    /**
     * @return string
     */
    public function getPersistenceDriver(): string
    {
        return $this->persistenceDriver;
    }

    /**
     * @return string
     */
    public function getPersistenceConnection(): string
    {
        return $this->persistenceConnection;
    }
}