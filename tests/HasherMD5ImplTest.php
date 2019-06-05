<?php

namespace Denismitr\Bloom\Tests;

use Denismitr\Bloom\Helpers\HasherMD5Impl;

class HasherMD5ImplTest extends TestCase
{
    /**
     * @var HasherMD5Impl
     */
    private $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = new HasherMD5Impl();
    }

    /**
     * @test
     * @dataProvider inputProvider
     * @param int $seed
     * @param string $input
     * @param int $expected
     */
    public function it_returns_an_absolute_number(int $seed, string $input, int $expected)
    {
        $this->assertEquals($expected, $this->hasher->hash($seed, $input));
    }

    public function inputProvider(): array
    {
        return [
            [1, 'abcde', 4086638794],
            [2, 'abcde', 2518994839],
            [3, 'abcde', 1539303330],
            [4, 'abcde', 352696981],
            [5, 'abcde', 758964640],
            [1, 'bvcf98', 4086638794],
            [2, 'bvcf98', 2518994839],
            [3, 'bvcf98', 1539303330],
            [4, 'bvcf98', 352696981],
            [5, 'bvcf98', 758964640],
            [1, '1245', 4086638794],
            [2, '1245', 2518994839],
            [3, '1245', 1539303330],
            [4, '1245', 352696981],
            [5, '1245', 758964640],
        ];
    }
}