<?php

namespace Mollie\Api\Types;

class PaymentMethod
{
    /**
     * @link https://www.mollie.com/en/payments/bancontact
     */
    const BANCONTACT = "bancontact";

    /**
     * @link https://www.mollie.com/en/payments/bank-transfer
     */
    const BANKTRANSFER = "banktransfer";

    /**
     * @link https://www.mollie.com/en/payments/belfius
     */
    const BELFIUS = "belfius";

    /**
     * @link https://www.mollie.com/en/payments/bitcoin
     */
    const BITCOIN = "bitcoin";

    /**
     * @link https://www.mollie.com/en/payments/credit-card
     */
    const CREDITCARD = "creditcard";

    /**
     * @link https://www.mollie.com/en/payments/direct-debit
     */
    const DIRECTDEBIT = "directdebit";

    /**
     * @link https://www.mollie.com/en/payments/eps
     */
    const EPS = "eps";

    /**
     * @link https://www.mollie.com/en/payments/gift-cards
     */
    const GIFTCARD = "giftcard";

    /**
     * @link https://www.mollie.com/en/payments/giropay
     */
    const GIROPAY = "giropay";

    /**
     * @link https://www.mollie.com/en/payments/ideal
     */
    const IDEAL = "ideal";

    /**
     * @link https://www.mollie.com/en/payments/ing-homepay
     */
    const INGHOMEPAY = "inghomepay";

    /**
     * @link https://www.mollie.com/en/payments/kbc-cbc
     */
    const KBC = "kbc";

    /**
     * @link https://www.mollie.com/en/payments/paypal
     */
    const PAYPAL = "paypal";

    /**
     * @link https://www.mollie.com/en/payments/paysafecard
     */
    const PAYSAFECARD = "paysafecard";

    /**
     * @deprecated
     * @link https://www.mollie.com/en/payments/gift-cards
     */
    const PODIUMCADEAUKAART = "podiumcadeaukaart";

    /**
     * @link https://www.mollie.com/en/payments/sofort
     */
    const SOFORT = "sofort";
}
