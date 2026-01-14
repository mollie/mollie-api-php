<?php

namespace Mollie\Api\Traits;

trait HasCurrencyConvenienceMethods
{
    /**
     * Create a Money object for AED currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function aed(string $value): static
    {
        return new static('AED', $value);
    }

    /**
     * Create a Money object for AUD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function aud(string $value): static
    {
        return new static('AUD', $value);
    }

    /**
     * Create a Money object for BGN currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function bgn(string $value): static
    {
        return new static('BGN', $value);
    }

    /**
     * Create a Money object for BRL currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function brl(string $value): static
    {
        return new static('BRL', $value);
    }

    /**
     * Create a Money object for CAD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function cad(string $value): static
    {
        return new static('CAD', $value);
    }

    /**
     * Create a Money object for CHF currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function chf(string $value): static
    {
        return new static('CHF', $value);
    }

    /**
     * Create a Money object for CZK currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function czk(string $value): static
    {
        return new static('CZK', $value);
    }

    /**
     * Create a Money object for DKK currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function dkk(string $value): static
    {
        return new static('DKK', $value);
    }

    /**
     * Create a Money object for EUR currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function euro(string $value): static
    {
        return new static('EUR', $value);
    }

    /**
     * Create a Money object for GBP currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function gbp(string $value): static
    {
        return new static('GBP', $value);
    }

    /**
     * Create a Money object for HKD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function hkd(string $value): static
    {
        return new static('HKD', $value);
    }

    /**
     * Create a Money object for HUF currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function huf(string $value): static
    {
        return new static('HUF', $value);
    }

    /**
     * Create a Money object for ILS currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function ils(string $value): static
    {
        return new static('ILS', $value);
    }

    /**
     * Create a Money object for ISK currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function isk(string $value): static
    {
        return new static('ISK', $value);
    }

    /**
     * Create a Money object for JPY currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function jpy(string $value): static
    {
        return new static('JPY', $value);
    }

    /**
     * Create a Money object for MXN currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function mxn(string $value): static
    {
        return new static('MXN', $value);
    }

    /**
     * Create a Money object for MYR currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function myr(string $value): static
    {
        return new static('MYR', $value);
    }

    /**
     * Create a Money object for NOK currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function nok(string $value): static
    {
        return new static('NOK', $value);
    }

    /**
     * Create a Money object for NZD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function nzd(string $value): static
    {
        return new static('NZD', $value);
    }

    /**
     * Create a Money object for PHP currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function php(string $value): static
    {
        return new static('PHP', $value);
    }

    /**
     * Create a Money object for PLN currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function pln(string $value): static
    {
        return new static('PLN', $value);
    }

    /**
     * Create a Money object for RON currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function ron(string $value): static
    {
        return new static('RON', $value);
    }

    /**
     * Create a Money object for RUB currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function rub(string $value): static
    {
        return new static('RUB', $value);
    }

    /**
     * Create a Money object for SEK currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function sek(string $value): static
    {
        return new static('SEK', $value);
    }

    /**
     * Create a Money object for SGD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function sgd(string $value): static
    {
        return new static('SGD', $value);
    }

    /**
     * Create a Money object for THB currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function thb(string $value): static
    {
        return new static('THB', $value);
    }

    /**
     * Create a Money object for TWD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function twd(string $value): static
    {
        return new static('TWD', $value);
    }

    /**
     * Create a Money object for USD currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function usd(string $value): static
    {
        return new static('USD', $value);
    }

    /**
     * Create a Money object for ZAR currency.
     *
     * @param string $value The amount value (e.g., '10.00')
     * @return static
     */
    public static function zar(string $value): static
    {
        return new static('ZAR', $value);
    }
}
