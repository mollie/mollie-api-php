<?php

namespace Mollie\Api\Contracts;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

interface PayloadRepository
{
    /**
     * @param  mixed  $value
     */
    public function set($value): self;

    /**
     * @return mixed
     */
    public function all();

    public function remove(string $key): self;

    public function add(string $key, $value): self;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    /**
     * Convert the body repository into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface;
}
