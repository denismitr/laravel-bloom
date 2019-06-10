<?php


namespace Denismitr\Bloom\Exceptions;


class UnsupportedBloomFilterPersistenceDriver extends BloomServiceException
{
    public static function driver($driver): self
    {
        $driver = is_string($driver) ? $driver : gettype($driver);

        return new static("Bloom filter persistence driver [{$driver}] is not supported.");
    } 
}