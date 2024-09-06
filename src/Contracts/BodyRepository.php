<?php

namespace Mollie\Api\Contracts;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

interface BodyRepository
{
    public function set(mixed $value): static;

    public function all(): mixed;

    public function remove(string $key): static;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    /**
     * Convert the body repository into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface;
}
