<?php

use Mollie\Api\MollieApiClient;

require_once dirname(__FILE__) . "/../vendor/autoload.php";

/*
 * Initialize the Mollie API library with your API key.
 *
 * See: https://www.mollie.com/dashboard/settings/profiles
 */
$mollie = new MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
