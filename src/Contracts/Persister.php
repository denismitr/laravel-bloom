<?php


namespace Denismitr\Bloom\Contracts;


use Denismitr\Bloom\Helpers\Indexes;
use Denismitr\Bloom\Helpers\Bits;

interface Persister
{
    public function setBits(string $key, Indexes $multi): void;

    public function getBits(string $key, Indexes $multi): Bits;
}