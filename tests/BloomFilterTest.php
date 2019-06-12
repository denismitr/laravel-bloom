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
     * @dataProvider multipleItems
     * @param array $itemsToAdd
     * @param array $itemsToMiss
     * @throws \Denismitr\Bloom\Exceptions\InvalidItemType
     */
    public function multiple_items_do_not_overlap(array $itemsToAdd, array $itemsToMiss)
    {
        foreach ($itemsToAdd as $item) {
            Bloom::key('user-recommendations')->add($item);
        }

        foreach ($itemsToAdd as $item) {
            $this->assertTrue(Bloom::key('user-recommendations')->test($item));
        }

        foreach ($itemsToMiss as $item) {
            $this->assertFalse(Bloom::key('user-recommendations')->test($item));
        }
    }

    /**
     * @test
     * @dataProvider multipleItemsUserSpecific
     * @param array $usersToAddTo
     * @param array $itemsToAdd
     * @param array $usersToMiss
     * @throws \Denismitr\Bloom\Exceptions\InvalidItemType
     */
    public function multiple_items_with_for_user_specific_key_do_not_overlap(array $usersToAddTo, array $itemsToAdd, array $usersToMiss)
    {
        foreach ($usersToAddTo as $user) {
            $bloomFilter = Bloom::key('user-recommendations', $user);

            foreach ($itemsToAdd as $item) {
                $bloomFilter->add($item);
            }
        }

        foreach ($usersToAddTo as $user) {
            $bloomFilter = Bloom::key('user-recommendations', $user);

            foreach ($itemsToAdd as $item) {
                $this->assertTrue($bloomFilter->test($item));
            }
        }

        foreach ($usersToMiss as $user) {
            $bloomFilter = Bloom::key('user-recommendations', $user);

            foreach ($itemsToAdd as $item) {
                $this->assertFalse($bloomFilter->test($item));
            }
        }
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
     * @throws \Denismitr\Bloom\Exceptions\InvalidItemType
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

        $bloomFilterA->clear();
        $bloomFilterB->clear();

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

        $bloomFilter->clear();

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
            ['userA' => 4, 'userB' => 456, 'recommendationA' => 567884, 'recommendationB' => 567889],
            ['u12345', 'u435t53463', 'i545895646', 'i42534534953468']
        ];
    }

    public function multipleItems(): array
    {
        return [
            [
                'itemsToAdd' => [1,2,3,4,5,6,7,8,9,10,11,12],
                'itemsToMiss' => [19, 20, 21, 22, 234, 23, 55, 90, 100, 101]
            ],
            [
                'itemsToAdd' => ['a','b','c','d','e','f','g','h','f','a1','b1','c4', 'e1', 'e3', 'f5', 'f6', 'a9', 'b56'],
                'itemsToMiss' => ['A', 'B', 'C4', 'C22', 'E45', 'G5', 'R55', 'A90', 'FF100', 'W101']
            ],
            [
                'itemsToAdd' => [1.5,2.9,3.3,4.5,5.7,6.5,7.124,8.0009,9.564,10.1111,11.2222,12.4321],
                'itemsToMiss' => [1.99, 2.1, 4.3, 5.5, 6.6, 7.987, 8.1119, 9.123, 11.9999, 12.654]
            ]
        ];
    }

    public function multipleItemsUserSpecific(): array
    {
        return [
            [
                'usersToAddTo' => [1,2,3,4,5,6,7,8,9,10],
                'itemsToAdd' => [1,2,3,4,5,6,7,8,9,10,11,12],
                'usersToMiss' => [19, 20, 21, 22, 234, 23, 55, 90, 100, 101]
            ],
            [
                'usersToAddTo' => [1,2,3,4,5,6,7,8,9,10,11,12],
                'itemsToAdd' => ['a','b','c','d','e','f','g','h','f','a1','b1','c4', 'e1', 'e3', 'f5', 'f6', 'a9', 'b56'],
                'usersToMiss' => [19, 20, 21, 22, 234, 23, 55, 90, 100, 101]
            ]
        ];
    }
}