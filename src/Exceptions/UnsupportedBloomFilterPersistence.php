<?php


namespace Denismitr\Bloom\Exceptions;


class UnsupportedBloomFilterPersistence extends BloomServiceException
{
    public static function because(string $message): self
    {
        return new static($message);
    } 
}