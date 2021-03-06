<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\BloomManager;
use Denismitr\Bloom\BloomFilter;
use Denismitr\Bloom\Exceptions\BloomServiceException;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterConfiguration;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterHashFunctionsNumber;
use Denismitr\Bloom\Exceptions\InvalidBloomFilterSize;
use Denismitr\Bloom\Exceptions\UnsupportedBloomFilterPersistence;
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
        $this->assertInstanceOf(BloomFilter::class, $manager->key('some-key'));
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
    public function it_can_instantiate_bloom_filter_with_key_specific_murmur_configuration()
    {
        config()->set('bloom.keys', [
            'user_recommendations' => [
                'size' => 550000,
                'num_hashes' => 4,
                'persistence' => [
                    'driver' => 'redis',
                    'connection' => 'default'
                ],
                'hashing_algorithm' => 'murmur',
            ]
        ]);

        $bloomFilter = Bloom::key('user_recommendations');

        $this->assertInstanceOf(BloomFilter::class, $bloomFilter);

        $this->assertEquals(550000, $bloomFilter->getSize());
        $this->assertEquals(4, $bloomFilter->getNumHashes());
    }

    /**
     * @test
     */
    public function it_throws_if_bloom_filter_size_is_too_large()
    {
        config()->set('bloom.default.size', 4294967297);

        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Size must not be greater than [4294967296] for the given perister driver: value [4294967297] is too large.');

        Bloom::key('some-key');
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

        $this->expectException(UnsupportedBloomFilterPersistence::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Bloom filter persistence driver must be a string, but [NULL] was given.');

        Bloom::key('some-key');
    }

    /**
     * @test
     */
    public function it_throw_if_redis_connection_is_incorrect()
    {
        config()->set('bloom.default.persistence.connection', 'incorrect-connection');

        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Redis connection [incorrect-connection] not configured.');

        Bloom::key('some-key');
    }

    /**
     * @test
     */
    public function it_throw_if_redis_connection_is_not_specified_correctly()
    {
        config()->set('bloom.default.persistence.connection', 0);

        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage('Bloom filter persistence connection must be a string, but [integer] was given.');

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

        $this->expectException(UnsupportedBloomFilterPersistence::class);
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
        config()->set('bloom.default.hashing_algorithm', 'sha256');

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
            'hashing_algorithm' => 'baz',
        ]);

        $this->expectException(UnsupportedHashingAlgorithm::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Unsupported hashing algorithm: baz.");

        Bloom::key('specific');
    }
    
    /**
     * @test
     */
    public function it_throws_if_default_size_is_not_an_unsigned_integer()
    {
        config()->set('bloom.default.size', -333);

        $this->expectException(InvalidBloomFilterSize::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Size must be a positive integer: value [-333] is invalid.");

        Bloom::key('any-key');
    }

    /**
     * @test
     */
    public function it_throws_if_key_specific_size_is_not_an_unsigned_integer()
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
        config()->set('bloom.default.num_hashes', 0);

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
        config()->set('bloom.keys.specific.num_hashes', 'foo');

        $this->expectException(InvalidBloomFilterHashFunctionsNumber::class);
        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Number of hash functions must be a positive integer: value [foo] is invalid.");

        Bloom::key('specific');
    }

    /**
     * @test
     * @dataProvider invalidBloomConfigurationPresets
     */
    public function it_throws_if_config_file_is_modified_incorrectly($preset)
    {
        config()->set('bloom', $preset);

        $this->expectException(InvalidBloomFilterConfiguration::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Bloom filter configuration file [bloom.php] is empty, invalid or misplaced.");

        Bloom::key('any-key');
    }

    public function invalidBloomConfigurationPresets(): array
    {
        return [
            [null],
            [ [] ],
            [ ['invalid' => []] ],
            [ ['default' => []] ],
        ];
    }
}