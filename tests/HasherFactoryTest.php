<?php


namespace Denismitr\Bloom\Tests;


use Denismitr\Bloom\Exceptions\BloomServiceException;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Factories\HasherFactory;
use Denismitr\Bloom\Helpers\HasherMD5Impl;
use Denismitr\Bloom\Helpers\HasherMurmurImpl;

class HasherFactoryTest extends TestCase
{
    /**
     * @var HasherFactory
     */
    private $hasherFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasherFactory = new HasherFactory();
    }

    /**
     * @test
     */
    public function it_can_create_md5_implementation()
    {
        $this->assertInstanceOf(HasherMD5Impl::class, $this->hasherFactory->make('md5'));
    }

    /**
     * @test
     */
    public function it_can_create_murmur_implementation()
    {
        $this->assertInstanceOf(HasherMurmurImpl::class, $this->hasherFactory->make('murmur'));
    }

    /**
     * @test
     */
    public function it_throws_on_unsupported_type()
    {
        $this->expectException(UnsupportedHashingAlgorithm::class);
        $this->expectException(BloomServiceException::class);
        $this->expectExceptionMessage("Unsupported hashing algorithm: invalid.");

        $this->hasherFactory->make('invalid');
    }
}