<?php

namespace Mollie\Api\Types;

class PaymentMethod
{
    /**
     * @link https://www.mollie.com/ideal
     */
    const IDEAL = "ideal";

    /**
     * @link https://www.mollie.com/paysafecard
     */
    const PAYSAFECARD = "paysafecard";

    /**
     * Credit card (includes Mastercard, Maestro, Visa and American Express).
     *
     * @link https://www.mollie.com/creditcard
     */
    const CREDITCARD = "creditcard";

    /**
     * Bancontact, formerly known as Mister Cash.
     *
     * @link https://www.mollie.com/mistercash
     */
    const MISTERCASH = "mistercash";

    /**
     * @link https://www.mollie.com/sofort
     */
    const SOFORT = "sofort";

    /**
     * @link https://www.mollie.com/banktransfer
     */
    const BANKTRANSFER = "banktransfer";

    /**
     * @link https://www.mollie.com/directdebit
     */
    const DIRECTDEBIT = "directdebit";

    /**
     * @link https://www.mollie.com/paypal
     */
    const PAYPAL = "paypal";

    /**
     * @link https://www.mollie.com/bitcoin
     */
    const BITCOIN = "bitcoin";

    /**
     * @link https://www.mollie.com/belfiusdirectnet
     */
    const BELFIUS = "belfius";

    /**
     * @deprecated
     * @link https://www.mollie.com/giftcards
     */
    const PODIUMCADEAUKAART = "podiumcadeaukaart";

    /**
     * @link https://www.mollie.com/nl/kbccbc
     */
    const KBC = "kbc";

    /**
     * @link https://www.mollie.com/nl/payments/ing-homepay
     */
    const INGHOMEPAY = "inghomepay";

    /**
     * Gift cards
     */
    const GIFTCARD = "giftcard";
}
