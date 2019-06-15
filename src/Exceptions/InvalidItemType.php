<?php


namespace Denismitr\Bloom\Exceptions;


class InvalidItemType extends BloomServiceException
{
    public static function item($item): self
    {
        $type = gettype($item);

        return new static("Item must be a non empty string or a number. Type {$type} is illegal.");
    }
}