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
        $this->assertTrue(Bloom::key('user-recommendations')->test($item));

        $this->assertFalse(
            Bloom::key('user-recommendations')->test('definitely-wrong-value')
        );
    }

    public function singleItemsToStore(): array
    {
        return [
            [1], ['1'], [12345], ['abc'], [19.8], [1000000], ['test-item-value']
        ];
    }
}