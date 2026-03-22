<?php

namespace Tests\Webhooks;

use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\Webhooks\SignatureValidator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class SignatureValidatorTest extends TestCase
{
    private const SIGNING_SECRET = 'test_webhook_secret';

    private const ALTERNATE_SECRET = 'alternate_webhook_secret';

    private const PAYLOAD = '{"id":"tr_12345","event_type":"payment-link.paid"}';

    /**
     * @test
     */
    public function verifies_valid_signature()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $signature = SignatureValidator::createSignature(self::PAYLOAD, self::SIGNING_SECRET);

        $result = $verifier->validatePayload(self::PAYLOAD, $signature);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function verifies_valid_signature_with_prefix()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $signature = 'sha256=' . SignatureValidator::createSignature(self::PAYLOAD, self::SIGNING_SECRET);

        $result = $verifier->validatePayload(self::PAYLOAD, $signature);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function verifies_valid_signature_from_multiple_secrets()
    {
        $verifier = new SignatureValidator([
            'invalid_secret',
            self::SIGNING_SECRET,
            'another_invalid_secret',
        ]);
        $signature = SignatureValidator::createSignature(self::PAYLOAD, self::SIGNING_SECRET);

        $result = $verifier->validatePayload(self::PAYLOAD, $signature);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function verifies_valid_signature_from_different_header_formats()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $signature = SignatureValidator::createSignature(self::PAYLOAD, self::SIGNING_SECRET);

        // Test with single string
        $result1 = $verifier->validatePayload(self::PAYLOAD, $signature);

        // Test with array of signatures
        $result2 = $verifier->validatePayload(self::PAYLOAD, [$signature]);

        // Test with multiple signatures where one is valid
        $result3 = $verifier->validatePayload(self::PAYLOAD, [
            'invalid_signature',
            $signature,
            'another_invalid_signature',
        ]);

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertTrue($result3);
    }

    /**
     * @test
     */
    public function accepts_any_valid_signature_during_migration()
    {
        $verifier = new SignatureValidator([
            self::SIGNING_SECRET,
            self::ALTERNATE_SECRET,
        ]);

        $originalSignature = SignatureValidator::createSignature(self::PAYLOAD, self::SIGNING_SECRET);
        $newSignature = SignatureValidator::createSignature(self::PAYLOAD, self::ALTERNATE_SECRET);

        $result1 = $verifier->validatePayload(self::PAYLOAD, $originalSignature);
        $result2 = $verifier->validatePayload(self::PAYLOAD, $newSignature);

        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_signature()
    {
        $this->expectException(InvalidSignatureException::class);

        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $verifier->validatePayload(self::PAYLOAD, 'invalid_signature');
    }

    /**
     * @test
     */
    public function returns_false_for_missing_signature()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $result = $verifier->validatePayload(self::PAYLOAD, []);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function static_verify_works_with_payload_and_signature()
    {
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SIGNING_SECRET);

        $result = SignatureValidator::validate(
            self::PAYLOAD,
            self::SIGNING_SECRET,
            $signature
        );

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function static_verify_works_with_request()
    {
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SIGNING_SECRET);

        // Mock PSR-7 request
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(self::PAYLOAD);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getHeader')->with('X-Mollie-Signature')->willReturn([$signature]);

        $result = SignatureValidator::validate($request, self::SIGNING_SECRET);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function verify_request_validates_signature_from_psr7_request()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SIGNING_SECRET);

        // Mock PSR-7 request
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(self::PAYLOAD);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getHeader')->with('X-Mollie-Signature')->willReturn([$signature]);

        $result = $verifier->validateRequest($request);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function verify_request_returns_false_for_legacy_webhook()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);

        // Mock PSR-7 request without signature header
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(self::PAYLOAD);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $request->method('getHeader')->with('X-Mollie-Signature')->willReturn([]);

        $result = $verifier->validateRequest($request);

        $this->assertFalse($result);
    }

    /** @test */
    public function can_handle_null_signatures_on_validate_payload()
    {
        $verifier = new SignatureValidator(self::SIGNING_SECRET);

        $isLegacyWebhook = ! $verifier->validatePayload(self::PAYLOAD, null);

        $this->assertTrue($isLegacyWebhook);
    }
}
