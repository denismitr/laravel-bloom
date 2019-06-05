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

        $indexes->each(function(int $index, int $key) use ($expectedIndexes) {
            $this->assertEquals($expectedIndexes[$key], $index);
        });
    }

    public function md5HasherDataProvider(): array
    {
        return [
            [5, "123", 30, [8, 8, 0, 1, 10]],
            [5, "123", 35, [28, 8, 5, 21, 0]],
            [5, "Test Title", 3000, [2588, 2678, 330, 1981, 640]],
        ];
    }
}