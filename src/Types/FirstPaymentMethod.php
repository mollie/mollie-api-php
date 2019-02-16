<?php

namespace Mollie\Api\Types;

class FirstPaymentMethod
{
    const CREDITCARD = 'creditcard';
    const BANCONTACT = 'bancontact';
    const BELFIUS = 'belfius';
    const EPS = 'eps';
    const GIROPAY = 'giropay';
    const IDEAL = 'ideal';
    const INGHOMEPAY = 'inghomepay';
    const KBC = 'kbc';
    const SOFORT = 'sofort';

    /**
     * Retrieve all methods that can be used for a first payment.
     *
     * @return array
     */
    public static function all()
    {
        return [
            static::CREDITCARD,
            static::BANCONTACT,
            static::BELFIUS,
            static::EPS,
            static::GIROPAY,
            static::IDEAL,
            static::INGHOMEPAY,
            static::KBC,
            static::SOFORT,
        ];
    }

    /**
     * Whether the method can be used as a first payment method.
     *
     * @param $method
     * @return bool
     */
    public static function exists($method)
    {
        return in_array($method, static::all());
    }
}