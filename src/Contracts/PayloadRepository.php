<?php

namespace Mollie\Api\Contracts;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

interface PayloadRepository extends Repository
{
    /**
     * Convert the repository contents into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface;
}
