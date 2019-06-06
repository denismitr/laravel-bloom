<?php


namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterImplementation extends BloomServiceException
{
    public static function because(string $message): self
    {
        return new static($message);
    } 
}