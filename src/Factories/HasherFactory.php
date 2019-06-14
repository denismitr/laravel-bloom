<?php


namespace Denismitr\Bloom\Factories;


use Denismitr\Bloom\Contracts\Hasher;
use Denismitr\Bloom\Exceptions\UnsupportedHashingAlgorithm;
use Denismitr\Bloom\Helpers\HasherMD5Impl;
use Denismitr\Bloom\Helpers\HasherMurmurImpl;

class HasherFactory
{
    const MD5_HASH_ALGORITHM = 'md5';
    const MURMUR_HASH_ALGORITHM = 'murmur';

    /**
     * @param $algorithm
     * @return Hasher
     * @throws UnsupportedHashingAlgorithm
     */
    public function make($algorithm): Hasher
    {
        if ( ! is_string($algorithm) ) {
            throw UnsupportedHashingAlgorithm::type( gettype($algorithm) );
        }

        switch(strtolower($algorithm)) {
            case self::MD5_HASH_ALGORITHM:
                return new HasherMD5Impl();
            case self::MURMUR_HASH_ALGORITHM:
                return new HasherMurmurImpl();
            default:
                throw UnsupportedHashingAlgorithm::algorithm($algorithm);
        }
    }
}