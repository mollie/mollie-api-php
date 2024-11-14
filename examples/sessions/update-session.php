<?php
/*
 * How to update an session with the Mollie API
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    $session = $mollie->sessions->get("sess_dfsklg13jO");
    $session->billingAddress->organizationName = "Mollie B.V.";
    $session->billingAddress->streetAndNumber = "Keizersgracht 126";
    $session->billingAddress->city = "Amsterdam";
    $session->billingAddress->region = "Noord-Holland";
    $session->billingAddress->postalCode = "1234AB";
    $session->billingAddress->country = "NL";
    $session->billingAddress->title = "Dhr";
    $session->billingAddress->givenName = "Piet";
    $session->billingAddress->familyName = "Mondriaan";
    $session->billingAddress->email = "piet@mondriaan.com";
    $session->billingAddress->phone = "+31208202070";
    $session->update();

    /*
     * Send the customer off to complete the order payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $session->getRedirectUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
