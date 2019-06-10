<?php


namespace Denismitr\Bloom\Exceptions;


class UnsupportedHashingAlgorithm extends BloomServiceException
{
    public static function algorithm(string $algorithm): self
    {
        return new static("Unsupported hashing algorithm: {$algorithm}.");
    }
}