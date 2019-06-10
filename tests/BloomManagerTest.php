<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\BloomManager;
use Denismitr\Bloom\BloomFilter;
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
                'persistence' => 'redis',
                'hashing_algorithm' => 'md5',
            ]
        ]);

        $bloomFilter = Bloom::key('user_recommendations');

        $this->assertInstanceOf(BloomFilter::class, $bloomFilter);

        $this->assertEquals(550, $bloomFilter->getSize());
        $this->assertEquals(10, $bloomFilter->getNumHashes());
    }
}