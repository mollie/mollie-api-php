<?php

namespace Mollie\Api\Exceptions;

/**
 * Thrown before a request is authenticated or sent when its URL points
 * somewhere other than a trusted API origin.
 *
 * Absolute URLs usually come from `_links.*.href` values in API responses or
 * webhook payloads. Refusing other origins guarantees the API key or access
 * token is never attached to a request for a host taken from such data, for
 * example a forged webhook payload whose signature was not verified.
 */
class UntrustedUrlException extends MollieException
{
    /** @var string */
    public $url;

    /** @var string */
    public $origin;

    /** @var string[] */
    public $trustedOrigins;

    /**
     * @param  string[]  $trustedOrigins
     */
    private function __construct(
        string $url,
        string $origin,
        array $trustedOrigins,
        string $message,
        ?\Throwable $previous = null
    ) {
        $this->url = $url;
        $this->origin = $origin;
        $this->trustedOrigins = $trustedOrigins;

        parent::__construct($message, 0, $previous);
    }

    /**
     * @param  string[]  $trustedOrigins
     */
    public static function untrustedOrigin(string $url, string $origin, array $trustedOrigins): self
    {
        return new self($url, $origin, $trustedOrigins, sprintf(
            'Refusing to send an authenticated request to untrusted origin "%s". Trusted origins: "%s".',
            $origin,
            implode('", "', $trustedOrigins)
        ));
    }

    /**
     * @param  string[]  $trustedOrigins
     */
    public static function unexpectedUserInfo(string $url, string $origin, array $trustedOrigins): self
    {
        return new self($url, $origin, $trustedOrigins, sprintf(
            'Refusing to send an authenticated request to "%s": the user info in the URL does not match the trusted origin.',
            $origin
        ));
    }

    /**
     * @param  string[]  $trustedOrigins
     */
    public static function malformedUrl(string $url, array $trustedOrigins, \Throwable $previous): self
    {
        return new self(
            $url,
            '',
            $trustedOrigins,
            'Refusing to send an authenticated request to a URL that cannot be parsed.',
            $previous
        );
    }
}
