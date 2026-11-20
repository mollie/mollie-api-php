<?php

namespace Mollie\Api\Utils;

use Mollie\Api\Exceptions\UntrustedUrlException;

/**
 * Validate full URLs before credentials are attached, without adding a PSR-7 dependency.
 */
class TrustedUrl
{
    /**
     * @param string $url
     * @param string[] $trustedUrls
     */
    public static function assertTrusted($url, array $trustedUrls)
    {
        $parts = self::parse($url);

        foreach ($trustedUrls as $trustedUrl) {
            $trusted = self::parse($trustedUrl);

            if ($parts !== null && $trusted !== null && $parts === $trusted) {
                return;
            }
        }

        // Do not include URLs or user info in the message: these can contain secrets.
        throw new UntrustedUrlException('Refusing to send an authenticated request to an untrusted or malformed URL.');
    }

    /**
     * Reject syntax interpreted differently by PHP, Guzzle and libcurl. Only the
     * scheme, host, effective port and exact user info participate in trust.
     *
     * @return array|null
     */
    private static function parse($url)
    {
        if (! is_string($url) || preg_match('/[\\x00-\\x20\\x7f\\\\\\\\]/', $url)
            || ! filter_var($url, FILTER_VALIDATE_URL)
            || ! preg_match('~^https?://([^/?#]+)~i', $url, $matches)
            || ! preg_match('/^(?:[^@]*@)?(?:[a-z0-9.-]+|\\[[a-f0-9:.]+\\])(?::[0-9]+)?$/i', $matches[1])) {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);

        return [
            $scheme,
            strtolower($parts['host']),
            isset($parts['port']) ? $parts['port'] : ($scheme === 'https' ? 443 : 80),
            isset($parts['user']) ? $parts['user'] : null,
            isset($parts['pass']) ? $parts['pass'] : null,
        ];
    }
}
