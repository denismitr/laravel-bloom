<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\Helpers\HasherMD5Impl;
use Denismitr\Bloom\Helpers\Indexer;

class IndexerTest extends TestCase
{
    /**
     * @var HasherMD5Impl
     */
    private $md5Hahser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->md5Hahser = new HasherMD5Impl();
    }

    /**
     * @test
     * @dataProvider md5HasherDataProvider
     * @param int $numHashes
     * @param string $value
     * @param int $size
     * @param array $expectedIndexes
     */
    public function it_can_get_5_indexes_with_md5_hasher(int $numHashes, string $value, int $size, array $expectedIndexes)
    {
        $indexer = new Indexer($this->md5Hahser);
        $indexes = $indexer->getIndexes($numHashes, $value, $size);

        $this->assertCount($numHashes, $indexes);

        foreach ($indexes->get() as $key=>$index) {
            $this->assertEquals($expectedIndexes[$key], $index);
        }
    }

    /**
     * @test
     * @dataProvider sizeLimitDataProvider
     * @param int $numHashes
     * @param string $value
     * @param int $size
     */
    public function it_should_always_be_withing_size_limits(int $numHashes, $value, int $size)
    {
        $indexer = new Indexer($this->md5Hahser);
        $indexes = $indexer->getIndexes($numHashes, $value, $size);

        $this->assertCount($numHashes, $indexes);

        foreach ($indexes->get() as $index) {
            $this->assertGreaterThanOrEqual(0, $index);
            $this->assertLessThan($size, $index);
        }
    }

    public function md5HasherDataProvider(): array
    {
        return [
            [5, "123", 30, [20, 11, 23, 16, 11]],
            [5, "123", 35, [5, 11, 18, 6, 1]],
            [5, "Test Title", 3000, [1531, 2340, 1656, 432, 1046]],
        ];
    }

    public function sizeLimitDataProvider(): array
    {
        return [
            [3, 11, 10],
            [4, 1, 15],
            [5, "123", 30],
            [5, "123", 35],
            [5, 6, 35],
            [5, "Test Title", 3000],
            [10, "Another test Title", 50],
            [10, 12345678, 100],
            [6, 987654321, 13],
            [5, 9, 9],
        ];
    }
}