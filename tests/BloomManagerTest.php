<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\BloomManager;
use Denismitr\Bloom\BloomFilter;
use Denismitr\Bloom\Contracts\Bloom;

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
    public function it_creates_a_redis_bloom_implementation_by_default()
    {
        $bloomRedisImpl = resolve(BloomManager::class)->key('hello');

        $this->assertInstanceOf(BloomFilter::class, $bloomRedisImpl);
    }
}