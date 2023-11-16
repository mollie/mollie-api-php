<?php
/*
 * List sessions using the Mollie API.
 */


try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * List the most recent sessions
     *
     * See: https://docs.mollie.com/reference/v2/sessions-api/list-sessions
     */
    echo '<ul>';
    $latestSessions = $mollie->sessions->page();
    printSessions($latestSessions);

    $previousSessions = $latestSessions->next();
    printSessions($previousSessions);
    echo '</ul>';
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}

function printSessions($sessions)
{
    if (empty($sessions)) {
        return;
    }

    foreach ($sessions as $session) {
        echo '<li><b>Session ' . htmlspecialchars($session->id) . ':</b> (' . htmlspecialchars($session->failedAt) . ')';
        echo '<br>Status: <b>' . htmlspecialchars($session->status);
        echo '<table border="1"><tr><th>Billed to</th><th>Shipped to</th><th>Total amount</th></tr>';
        echo '<tr>';
        echo '<td>' . htmlspecialchars($session->shippingAddress->givenName) . ' ' . htmlspecialchars($session->shippingAddress->familyName) . '</td>';
        echo '<td>' . htmlspecialchars($session->billingAddress->givenName) . ' ' . htmlspecialchars($session->billingAddress->familyName) . '</td>';
        echo '<td>' . htmlspecialchars($session->amount->currency) . str_replace('.', ',', htmlspecialchars($session->amount->value)) . '</td>';
        echo '</tr>';
        echo '</table>';
        echo '<a href="' . $session->getRedirectUrl() . '" target="_blank">Click here to pay</a>';
        echo '</li>';
    }
}
