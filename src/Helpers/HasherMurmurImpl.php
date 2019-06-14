<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Helpers;


use lastguest\Murmur;
use Denismitr\Bloom\Contracts\Hasher;

class HasherMurmurImpl implements Hasher
{
    /**
     * @param int $seed
     * @param string $value
     * @return int
     */
    public function hash(int $seed, string $value): int
    {
        $input = sprintf("%d__%s", $seed, $value);

        return Murmur::hash3_int($input);
    }
}