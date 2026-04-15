<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

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
    case Bitcoin = 'bitcoin';
    case Blik = 'blik';
    case Creditcard = 'creditcard';
    case Directdebit = 'directdebit';
    case Eps = 'eps';
    case Giftcard = 'giftcard';
    case Giropay = 'giropay';
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
}
