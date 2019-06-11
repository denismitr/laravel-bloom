<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\BloomManager;
use Denismitr\Bloom\BloomFilter;
use Denismitr\Bloom\Exceptions\BloomServiceException;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterHashFunctionsNumber;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterSize;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistenceDriver;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Facades\Bloom;

class BloomManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_instantiated()
    {
        $manager = resolve(BloomManager::class);

        $this->assertInstanceOf(BloomManager::class, $manager);
    }

    /**
     * @test
     */
    public function it_creates_a_bloom_filter_with_default_config_settings_if_key_has_no_own_params()
    {
        $bloomFilter = Bloom::key('some-key');

        $this->assertInstanceOf(BloomFilter::class, $bloomFilter);
    }

    /**
     * @test
     */
    public function it_can_instantiate_bloom_filter_with_key_specific_configuration()
    {
        config()->set('bloom.keys', [
            'user_recommendations' => [
                'size' => 550,
                'num_hashes' => 10,
                'persistence' => [
                    'driver' => 'redis',
                    'connection' => 'default'
                ],
                'hashing_algorithm' => 'md5',
            ]
        ]);

        $bloomFilter = Bloom::key('user_recommendations');

        $this->assertInstanceOf(BloomFilter::class, $bloomFilter);

        $this->assertEquals(550, $bloomFilter->getSize());
        $this->assertEquals(10, $bloomFilter->getNumHashes());
    }

    /**
     * @test
     */
    public function it_throws_if_default_persistence_driver_is_unsupported()
    {
        config()->set('bloom.default', [
            'size' => 333000,
            'num_hashes' => 3,
            'persistence' => 'invalid',
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(UnsupportedBloomFilterPersistenceDriver::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Bloom filter persistence driver must be a string, but [NULL] was given.');

        Bloom::key('some-key');
    }

    /**
     * @test
     */
    public function it_throws_if_key_specific_persistence_driver_is_unsupported()
    {
        config()->set('bloom.keys.some-specific-key', [
            'size' => 333000,
            'num_hashes' => 3,
            'persistence' => [
                'driver' => 'mysql',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(UnsupportedBloomFilterPersistenceDriver::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Bloom filter persistence driver [mysql] is not supported.');

        Bloom::key('some-specific-key');
    }

    /**
     * @test
     */
    public function it_throws_if_default_hashing_algorithm_is_unsupported()
    {
        config()->set('bloom.default', [
            'size' => 333000,
            'num_hashes' => 3,
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'sha256',
        ]);

        $this->expectException(UnsupportedHashingAlgorithm::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Unsupported hashing algorithm: sha256.");

        Bloom::key('some-other-key');
    }

    /**
     * @test
     */
    public function it_throws_if_key_specific_hashing_algorithm_is_unsupported()
    {
        config()->set('bloom.keys.specific', [
            'size' => 333000,
            'num_hashes' => 3,
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'sha512',
        ]);

        $this->expectException(UnsupportedHashingAlgorithm::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Unsupported hashing algorithm: sha512.");

        Bloom::key('specific');
    }
    
    /**
     * @test
     */
    public function it_throws_if_default_price_is_not_an_unsigned_integer()
    {
        config()->set('bloom.default', [
            'size' => -333,
            'num_hashes' => 3,
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(InvalidBloomFilterSize::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Size must be a positive integer: value [-333] is invalid.");

        Bloom::key('any-key');
    }

    /**
     * @test
     */
    public function it_throws_if_key_specific_price_is_not_an_unsigned_integer()
    {
        config()->set('bloom.keys.specific', [
            'size' => 'boo',
            'num_hashes' => 3,
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(InvalidBloomFilterSize::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Size must be a positive integer: value [boo] is invalid.");

        Bloom::key('specific');
    }

    /**
     * @test
     */
    public function it_throws_if_default_number_of_hash_functions_is_not_a_positive_integer()
    {
        config()->set('bloom.default', [
            'size' => 300,
            'num_hashes' => 0,
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(InvalidBloomFilterHashFunctionsNumber::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Number of hash functions must be a positive integer: value [0] is invalid.");

        Bloom::key('some-key');
    }

    /**
     * @test
     */
    public function it_throws_if_key_specific_number_of_hash_functions_is_not_a_positive_integer()
    {
        config()->set('bloom.keys.specific', [
            'size' => 300,
            'num_hashes' => 'foo',
            'persistence' => [
                'driver' => 'redis',
                'connection' => 'default'
            ],
            'hashing_algorithm' => 'md5',
        ]);

        $this->expectException(InvalidBloomFilterHashFunctionsNumber::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Number of hash functions must be a positive integer: value [foo] is invalid.");

        Bloom::key('specific');
    }
}