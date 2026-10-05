<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

/**
 * Payment method identifiers known to this SDK release.
 *
 * This is SDK vocabulary, not an allow-list: Mollie can accept or return a
 * method that is not listed here, and such values reach you as raw strings
 * through the `PaymentMethod|string` property types. Use the Methods API to
 * learn which methods are enabled on a profile.
 */
enum PaymentMethod: string
{
    case Alma = 'alma';
    case Applepay = 'applepay';
    case Bacs = 'bacs';
    case Bancomatpay = 'bancomatpay';
    case Bancontact = 'bancontact';
    case Banktransfer = 'banktransfer';
    case Belfius = 'belfius';
    case Billie = 'billie';
    case Billink = 'billink';
    case Bitcoin = 'bitcoin';
    case Bizum = 'bizum';
    case Blik = 'blik';
    case Creditcard = 'creditcard';
    case Directdebit = 'directdebit';
    case Eps = 'eps';
    case Giftcard = 'giftcard';
    case Giropay = 'giropay';
    /**
     * Wallet identifier for the Methods API's `includeWallets` parameter.
     * This value is not a valid `method` for Create payment.
     *
     * For hosted checkout, use {@see PaymentMethod::Creditcard}. Google Pay
     * appears automatically when enabled on the profile and supported by the
     * customer's device and browser. For direct integration, also pass the
     * `googlePayPaymentToken` field on the create payment request.
     *
     * @link https://docs.mollie.com/docs/google-pay
     * @link https://docs.mollie.com/docs/direct-integration-of-google-pay
     * @link https://docs.mollie.com/reference/list-methods
     */
    case Googlepay = 'googlepay';
    case Swish = 'swish';
    case In3 = 'in3';
    case Ideal = 'ideal';
    case Inghomepay = 'inghomepay';
    case Kbc = 'kbc';
    case KlarnaOne = 'klarna';
    case KlarnaPayLater = 'klarnapaylater';
    case KlarnaPayNow = 'klarnapaynow';
    case KlarnaSliceIt = 'klarnasliceit';
    case Mbway = 'mbway';
    case Mobilepay = 'mobilepay';
    case Multibanco = 'multibanco';
    case Mybank = 'mybank';
    case Payconiq = 'payconiq';
    case Paypal = 'paypal';
    case Paysafecard = 'paysafecard';
    case Paybybank = 'paybybank';
    case Podiumcadeaukaart = 'podiumcadeaukaart';
    case PointOfSale = 'pointofsale';
    case Przelewy24 = 'przelewy24';
    case Satispay = 'satispay';
    case Sofort = 'sofort';
    case Riverty = 'riverty';
    case Trustly = 'trustly';
    case Twint = 'twint';
    case Vipps = 'vipps';
    case Voucher = 'voucher';
    case Wero = 'wero';
}
