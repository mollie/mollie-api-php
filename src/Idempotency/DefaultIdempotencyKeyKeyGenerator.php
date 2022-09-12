<?php
namespace Mollie\Api\Idempotency;

class DefaultIdempotencyKeyKeyGenerator implements IdempotencyKeyGeneratorContract
{
    const LENGTH = 16;

    /**
     * @throws \Exception
     * @return string
     */
    public function generate()
    {
        $string = '';

        while (($length = strlen($string)) < self::LENGTH) {
            $size = self::LENGTH - $length;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
