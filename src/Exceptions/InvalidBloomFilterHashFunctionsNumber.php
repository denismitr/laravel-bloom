<?php


namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterHashFunctionsNumber extends BloomServiceException
{
    public static function number($number): self
    {
        return new static("Number of hash functions must be a positive integer: value [{$number}] is invalid.");
    }
}