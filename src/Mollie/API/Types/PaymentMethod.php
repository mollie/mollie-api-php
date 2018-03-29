<?php

namespace Mollie\Api\Types;

class PaymentMethod
{
    /**
     * @link https://mollie.com/ideal
     */
    const IDEAL = "ideal";

    /**
     * @link https://mollie.com/paysafecard
     */
    const PAYSAFECARD = "paysafecard";

    /**
     * Credit card (includes Mastercard, Maestro, Visa and American Express).
     *
     * @link https://mollie.com/creditcard
     */
    const CREDITCARD = "creditcard";

    /**
     * Bancontact, formerly known as Mister Cash.
     *
     * @link https://mollie.com/mistercash
     */
    const MISTERCASH = "mistercash";

    /**
     * @link https://mollie.com/sofort
     */
    const SOFORT = "sofort";

    /**
     * @link https://mollie.com/banktransfer
     */
    const BANKTRANSFER = "banktransfer";

    /**
     * @link https://mollie.com/directdebit
     */
    const DIRECTDEBIT = "directdebit";

    /**
     * @link https://mollie.com/paypal
     */
    const PAYPAL = "paypal";

    /**
     * @link https://mollie.com/bitcoin
     */
    const BITCOIN = "bitcoin";

    /**
     * @link https://mollie.com/belfiusdirectnet
     */
    const BELFIUS = "belfius";

    /**
     * @deprecated
     * @link https://mollie.com/giftcards
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

    /**
     * This is a special method that indicates in the API that the payment consists of several partial transactions.
     *
     * The individual transactions (amount, method and details) can be found in the details property of the payment.
     *
     * Note that you cannot use this method to create payments.
     *
     * @internal
     */
    const STACKED = "stacked";
}
