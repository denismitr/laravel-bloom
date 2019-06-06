<?php


namespace Denismitr\Bloom\Exceptions;


class InvalidHasherImplementation extends BloomServiceException
{
    public static function because(string $message): self
    {
        return new static($message);
    }
}