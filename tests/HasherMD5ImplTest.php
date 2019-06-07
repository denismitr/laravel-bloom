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
            [1, 'abcde', 3987542742],
            [2, 'abcde', 2620839345],
            [3, 'abcde', 395677500],
            [4, 'abcde', 756302468],
            [5, 'abcde', 2503472180],
            [1, 'bvcf98', 138841924],
            [2, 'bvcf98', 222273853],
            [3, 'bvcf98', 3515454493],
            [4, 'bvcf98', 3396287773],
            [5, 'bvcf98', 301822942],
            [1, '1245', 2199149011],
            [2, '1245', 3036544315],
            [3, '1245', 1575866235],
            [4, '1245', 3681455173],
            [5, '1245', 2546561598],
        ];
    }
}