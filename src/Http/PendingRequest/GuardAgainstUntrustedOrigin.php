<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Contracts\DefinesTrustedOrigins;
use Mollie\Api\Exceptions\UntrustedUrlException;
use Mollie\Api\Http\PendingRequest;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Refuses requests whose URL is not on a trusted origin.
 *
 * Absolute URLs bypass the base URL (see Url::join()) and usually come from
 * `_links.*.href` in response or webhook data. This handler must run before
 * AuthenticateRequest so credentials are never attached to such a request.
 *
 * Origins are derived with the transport's own URI factory, so the check sees
 * exactly the scheme, user info, host and port the request will be sent to.
 */
class GuardAgainstUntrustedOrigin
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $connector = $pendingRequest->getConnector();
        $uriFactory = $pendingRequest->getFactoryCollection()->uriFactory;
        $url = $pendingRequest->url();

        $trustedUris = array_map(
            fn (string $trustedUrl): UriInterface => $uriFactory->createUri($trustedUrl),
            $connector instanceof DefinesTrustedOrigins
                ? $connector->trustedOrigins()
                : [$connector->resolveBaseUrl()]
        );

        $trustedOrigins = array_values(array_unique(array_map(
            fn (UriInterface $uri): string => self::originOf($uri),
            $trustedUris
        )));

        $uri = $this->parse($uriFactory, $url, $trustedOrigins);
        $origin = self::originOf($uri);

        if (! in_array($origin, $trustedOrigins, true)) {
            throw UntrustedUrlException::untrustedOrigin($url, $origin, $trustedOrigins);
        }

        foreach ($trustedUris as $trustedUri) {
            if (self::originOf($trustedUri) === $origin && $trustedUri->getUserInfo() === $uri->getUserInfo()) {
                return $pendingRequest;
            }
        }

        throw UntrustedUrlException::unexpectedUserInfo($url, $origin, $trustedOrigins);
    }

    /**
     * @param  string[]  $trustedOrigins
     */
    private function parse(UriFactoryInterface $uriFactory, string $url, array $trustedOrigins): UriInterface
    {
        try {
            return $uriFactory->createUri($url);
        } catch (\InvalidArgumentException $e) {
            throw UntrustedUrlException::malformedUrl($url, $trustedOrigins, $e);
        }
    }

    /**
     * PSR-7 lowercases scheme and host and reports default ports as null.
     * User info is left out so it never ends up in exception messages.
     */
    private static function originOf(UriInterface $uri): string
    {
        $port = $uri->getPort();

        return $uri->getScheme().'://'.$uri->getHost().($port !== null ? ':'.$port : '');
    }
}
