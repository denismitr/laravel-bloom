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

    public function md5HasherDataProvider(): array
    {
        return [
            [5, "123", 30, [20, 11, 23, 16, 11]],
            [5, "123", 35, [5, 11, 18, 6, 1]],
            [5, "Test Title", 3000, [1531, 2340, 1656, 432, 1046]],
        ];
    }
}