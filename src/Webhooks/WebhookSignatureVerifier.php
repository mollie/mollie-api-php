<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Utils\Arr;
use Psr\Http\Message\RequestInterface;

class SignatureValidator
{
    /**
     * Signature header name.
     */
    private const SIGNATURE_HEADER = 'X-Mollie-Signature';

    /**
     * Signature prefix in header.
     */
    private const SIGNATURE_PREFIX = 'sha256=';

    /**
     * Signing secret(s) used to verify signatures.
     * Can be a single string or an array of strings for migration periods.
     *
     * @var string[]
     */
    private array $signingSecrets;

    /**
     * Create a new WebhookSignatureVerifier instance.
     *
     * @param string|string[] $signingSecrets One or more signing secrets
     */
    public function __construct($signingSecrets)
    {
        $this->signingSecrets = Arr::wrap($signingSecrets);
    }

    /**
     * Verify a PSR-7 compatible request.
     *
     * @param RequestInterface $request The PSR-7 request
     * @return bool True if valid, false if legacy webhook (no signature)
     * @throws InvalidSignatureException If signature validation fails
     */
    public function validateRequest(RequestInterface $request): bool
    {
        $body = (string) $request->getBody();
        $signatures = $request->getHeader(self::SIGNATURE_HEADER);

        if (empty($signatures)) {
            // No signatures found - treat as legacy webhook
            return false;
        }

        return $this->validateSignatures($body, $signatures);
    }

    /**
     * Verify webhook payload with provided signature(s).
     *
     * @param string $payload Raw request body
     * @param string|string[] $signatures One or more signatures to validate
     * @return bool True if any signature is valid
     * @throws InvalidSignatureException If all signatures are invalid
     */
    public function validatePayload(string $payload, $signatures): bool
    {
        $signatures = is_array($signatures) ? $signatures : [$signatures];

        if (empty($signatures)) {
            // No signatures found - treat as legacy webhook
            return false;
        }

        return $this->validateSignatures($payload, $signatures);
    }

    /**
     * Verify signatures against all configured signing secrets.
     *
     * @param string $payload The raw request payload
     * @param array $signatures The signatures to verify
     * @return bool True if any signature matches any secret
     * @throws InvalidSignatureException If all signatures are invalid
     */
    private function validateSignatures(string $payload, array $signatures): bool
    {
        $validSignatureFound = false;

        foreach ($signatures as $signatureHeader) {
            // Extract the raw signature value by removing the prefix
            if (strpos($signatureHeader, self::SIGNATURE_PREFIX) === 0) {
                $providedSignature = substr($signatureHeader, strlen(self::SIGNATURE_PREFIX));
            } else {
                $providedSignature = $signatureHeader;
            }

            // Try each signing secret
            foreach ($this->signingSecrets as $secret) {
                $expectedSignature = hash_hmac('sha256', $payload, $secret);

                if (hash_equals($expectedSignature, $providedSignature)) {
                    $validSignatureFound = true;
                    break 2; // Valid signature found, exit both loops
                }
            }
        }

        if (!$validSignatureFound && !empty($signatures)) {
            throw new InvalidSignatureException('Invalid webhook signature');
        }

        return $validSignatureFound;
    }

    /**
     * Static method for simpler usage.
     *
     * @param RequestInterface|string $input PSR-7 request or raw payload
     * @param string|string[] $signingSecrets One or more signing secrets
     * @param string|string[]|null $signatures Required if $input is a string payload
     * @return bool True if valid signature or legacy webhook
     * @throws InvalidSignatureException If all signatures are invalid
     */
    public static function validate($input, $signingSecrets, $signatures = null): bool
    {
        $verifier = new self($signingSecrets);

        if ($input instanceof RequestInterface) {
            return $verifier->validateRequest($input);
        }

        if (is_string($input) && ($signatures !== null)) {
            return $verifier->validatePayload($input, $signatures);
        }

        throw new InvalidSignatureException('Unsupported input type');
    }
}
