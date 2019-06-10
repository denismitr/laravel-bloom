<?php


namespace Denismitr\Bloom\Exceptions;


class UnsupportedBloomFilterPersistenceDriver extends BloomServiceException
{
    public static function driver(string $driver): self
    {
        return new static("Bloom filter persistence driver [{$driver}] is not supported.");
    } 
}