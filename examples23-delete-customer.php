<?php
try
{
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";
    
    $mollie->customers->delete("customer_id");

}
catch (Mollie_API_Exception $e)
{
    error_log( "API call failed: " . htmlspecialchars($e->getMessage()));
}

