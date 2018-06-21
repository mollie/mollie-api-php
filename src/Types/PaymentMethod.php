<?php

namespace Mollie\Api\Types;

class PaymentMethod
{
    /**
     * Bancontact
     *
     * @link https://www.mollie.com/bancontact
     */
    const BANCONTACT = "bancontact";

    /**
     * @link https://www.mollie.com/banktransfer
     */
    const BANKTRANSFER = "banktransfer";

    /**
     * @link https://www.mollie.com/belfiusdirectnet
     */
    const BELFIUS = "belfius";

    /**
     * @link https://www.mollie.com/bitcoin
     */
    const BITCOIN = "bitcoin";

    /**
     * Credit card (includes Mastercard, Maestro, Visa and American Express).
     *
     * @link https://www.mollie.com/creditcard
     */
    const CREDITCARD = "creditcard";

    /**
     * @link https://www.mollie.com/directdebit
     */
    const DIRECTDEBIT = "directdebit";

    /**
     * TODO: Add link?
     */
    const EPS = "eps";

    /**
     * @link https://www.mollie.com/gift-cards
     */
    const GIFTCARD = "giftcard";

    /**
     * TODO: Add link?
     */
    const GIROPAY = "giropay";

    /**
     * @link https://www.mollie.com/ideal
     */
    const IDEAL = "ideal";

    /**
     * @link https://www.mollie.com/ing-homepay
     */
    const INGHOMEPAY = "inghomepay";

    /**
     * @link https://www.mollie.com/kbccbc
     */
    const KBC = "kbc";

    /**
     * @link https://www.mollie.com/paypal
     */
    const PAYPAL = "paypal";

    /**
     * @link https://www.mollie.com/paysafecard
     */
    const PAYSAFECARD = "paysafecard";

    /**
     * @deprecated
     * @link https://www.mollie.com/gift-cards
     */
    const PODIUMCADEAUKAART = "podiumcadeaukaart";

    /**
     * @link https://www.mollie.com/sofort
     */
    const SOFORT = "sofort";
}
