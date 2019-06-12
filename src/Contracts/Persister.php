<?php


namespace Denismitr\Bloom\Contracts;


use Denismitr\Bloom\Helpers\Indexes;
use Denismitr\Bloom\Helpers\Bits;

/**
 * Interface Persister
 * @package Denismitr\Bloom\Contracts
 */
interface Persister
{
    /**
     * @param string $key
     * @param Indexes $multi
     */
    public function setBits(string $key, Indexes $multi): void;

    /**
     * @param string $key
     * @param Indexes $multi
     * @return Bits
     */
    public function getBits(string $key, Indexes $multi): Bits;

    /**
     * @param string $key
     */
    public function clear(string $key): void;

    /**
     * @return int
     */
    public function getMaxCapacity(): int;
}