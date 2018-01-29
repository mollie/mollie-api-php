<?php
/*
Example 23 - Delete a customer from mollie api.
*/

try
{
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";
    
    $mollie->customers->delete("customer_id");
    echo "Customer deleted!";

}
catch (Mollie_API_Exception $e)
{
    error_log( "API call failed: " . htmlspecialchars($e->getMessage()));
}

