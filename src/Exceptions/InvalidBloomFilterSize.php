<?php


namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterSize extends BloomServiceException
{
    public static function size($size): self
    {
        return new static("Size must be a positive integer: value [{$size}] is invalid.");
    }
}