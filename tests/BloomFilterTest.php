<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\Facades\Bloom;
use Illuminate\Support\Facades\Redis;

class BloomFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Redis::flushAll();
    }

    /**
     */
    public function it_can_store_key_in_via_redis()
    {
        Bloom::key('user-recommendations')->add(1);

        $this->assertEquals(1, Redis::getbit('user-recommendations', 77588));
        $this->assertEquals(1, Redis::getbit('user-recommendations', 89678));
        $this->assertEquals(1, Redis::getbit('user-recommendations', 3330));
        $this->assertEquals(1, Redis::getbit('user-recommendations', 96981));
        $this->assertEquals(1, Redis::getbit('user-recommendations', 64640));

        $this->assertTrue(Bloom::key('user-recommendations')->test(1));
    }

    /**
     * @test
     * @dataProvider singleItemsToStore
     */
    public function it_can_verify_stored_single_result($item)
    {
        Bloom::key('user-recommendations')->add($item);

        $this->assertTrue(
            Bloom::key('user-recommendations')->test($item),
            "Given item {$item} not found in Bloom filter"
        );

        $this->assertFalse(
            Bloom::key('user-recommendations')->test('definitely-wrong-value'),
            "Wrong key found in the Bloom filter"
        );
    }

    /**
     * @test
     * @dataProvider keysWithSuffixesDataProvider
     * @param int|string $userA
     * @param int|string $userB
     * @param int|string $recommendationA
     * @param int|string $recommendationB
     */
    public function it_can_use_key_suffix($userA, $userB, $recommendationA, $recommendationB)
    {
        $bloomFilterA = Bloom::key('user-recommendations', $userA);
        $bloomFilterB = Bloom::key('user-recommendations', $userB);

        $bloomFilterA->add($recommendationA);
        $bloomFilterB->add($recommendationB);

        $this->assertTrue($bloomFilterA->test($recommendationA));
        $this->assertTrue($bloomFilterB->test($recommendationB));

        $this->assertFalse($bloomFilterA->test($recommendationB));
        $this->assertFalse($bloomFilterB->test($recommendationA));
    }

    /**
     * @test
     * @dataProvider keysWithSuffixesDataProvider
     * @param int|string $userA
     * @param int|string $userB
     * @param int|string $recommendationA
     * @param int|string $recommendationB
     */
    public function it_can_use_key_suffix_for_reset_as_well($userA, $userB, $recommendationA, $recommendationB)
    {
        $bloomFilterA = Bloom::key('user-recommendations', $userA);
        $bloomFilterB = Bloom::key('user-recommendations', $userB);

        $bloomFilterA->add($recommendationA);
        $bloomFilterB->add($recommendationB);

        $this->assertTrue($bloomFilterA->test($recommendationA));
        $this->assertTrue($bloomFilterB->test($recommendationB));

        $this->assertFalse($bloomFilterA->test($recommendationB));
        $this->assertFalse($bloomFilterB->test($recommendationA));

        $bloomFilterA->reset();
        $bloomFilterB->reset();

        $this->assertFalse($bloomFilterA->test($recommendationA));
        $this->assertFalse($bloomFilterB->test($recommendationB));
        $this->assertFalse($bloomFilterA->test($recommendationB));
        $this->assertFalse($bloomFilterB->test($recommendationA));
    }

    /**
     * @test
     */
    public function it_can_reset_a_given_key()
    {
        $bloomFilter = Bloom::key('key-in-need-of-reset');

        $bloomFilter->add(155);

        $this->assertTrue($bloomFilter->test(155));

        $bloomFilter->reset();

        $this->assertFalse($bloomFilter->test(155));
    }

    public function singleItemsToStore(): array
    {
        return [
            [1], ['1'], [12345], ['abc'], [19.8], [1000000], ['test-item-value']
        ];
    }

    public function keysWithSuffixesDataProvider(): array
    {
        return [
            [4, 456, 567884, 567889],
            ['u12345', 'u435t53463', 'i545895646', 'i42534534953468']
        ];
    }
}