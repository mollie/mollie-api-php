<?php

/*
 * How to create a new client link in the Mollie API.
 */

use Mollie\Api\Http\Payload\CreateClientLinkPayload;
use Mollie\Api\Http\Payload\Owner;
use Mollie\Api\Http\Payload\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require '../initialize.php';

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? 'https' : 'http';
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF']);

    /*
     * ClientLink creation parameters.
     *
     * See: https://docs.mollie.com/reference/v2/client-links-api/create-client-link
     */
    $response = $mollie->send(new CreateClientLinkRequest(new CreateClientLinkPayload(
        new Owner('foo@test.com', 'foo', 'bar', 'nl_NL'),
        'Foo Company',
        new OwnerAddress('NL', 'Keizersgracht 313', '1016 EE', 'Amsterdam'),
        '30204462',
        'NL123456789B01',
    )));

    $clientLink = $response->toResource();

    /**
     * Get the redirect url for the client link, by passing in the 'client_id' of the your app,
     * a random generated string as 'state' to prevent CSRF attacks.  This will be reflected in
     * the state query parameter when the user returns to the redirect_uri after authorizing your app.
     *
     * For more info see: https://docs.mollie.com/reference/oauth2/authorize#parameters
     */
    $redirectUrl = $clientLink->getRedirectUrl('app_j9Pakf56Ajta6Y65AkdTtAv', 'decafbad', 'force', [
        'onboarding.read',
        'onboarding.write',
    ]);

    /*
     * Send the customer off to finalize the organization creation.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header('Location: '.$redirectUrl, true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: '.htmlspecialchars($e->getMessage());
}
