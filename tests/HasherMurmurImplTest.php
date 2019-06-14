<?php

namespace Denismitr\Bloom\Tests;

use Denismitr\Bloom\Helpers\HasherMurmurImpl;

class HasherMurmurImplTest extends TestCase
{
    /**
     * @var HasherMurmurImpl
     */
    private $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = new HasherMurmurImpl();
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
            [1, 'abcde', 1136011894],
            [2, 'abcde', 4130266590],
            [3, 'abcde', 4172215556],
            [4, 'abcde', 4271741066],
            [5, 'abcde', 1095729086],
            [1, 'bvcf98', 1431662988],
            [2, 'bvcf98', 2216308768],
            [3, 'bvcf98', 2374031575],
            [4, 'bvcf98', 2952500017],
            [5, 'bvcf98', 3624708494],
            [1, '1245', 1070603143],
            [2, '1245', 3497760477],
            [3, '1245', 2034118607],
            [4, '1245', 2961905948],
            [5, '1245', 570119241],
        ];
    }
}