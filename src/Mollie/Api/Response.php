<?php

abstract class Mollie_Api_Response
{
	abstract public function __construct (stdClass $json_response);
}