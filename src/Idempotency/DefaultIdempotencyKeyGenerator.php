<?php

namespace Mollie\Api\Idempotency;

use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;
use Mollie\Api\Exceptions\IncompatiblePlatformException;

class DefaultIdempotencyKeyGenerator implements IdempotencyKeyGeneratorContract
{
    const DEFAULT_LENGTH = 16;

    protected int $length;

    public function __construct($length = self::DEFAULT_LENGTH)
    {
        $this->length = $length;
    }

    /**
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatformException
     */
    public function generate(): string
    {
        $length = $this->length;

        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            try {
                $bytes = random_bytes($size);
            } catch (\Exception $e) {
                throw new IncompatiblePlatformException(
                    'PHP function random_bytes missing. Consider overriding the DefaultIdempotencyKeyGenerator with your own.',
                    IncompatiblePlatformException::INCOMPATIBLE_RANDOM_BYTES_FUNCTION
                );
            }

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
