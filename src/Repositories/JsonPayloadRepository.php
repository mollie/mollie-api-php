<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Contracts\Stringable;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class JsonPayloadRepository extends ArrayStore implements PayloadRepository, Stringable
{
    public function __toString(): string
    {
        return ! empty($this->store) ? @json_encode($this->store) : '';
    }

    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface
    {
        return $streamFactory->createStream((string) $this);
    }
}
