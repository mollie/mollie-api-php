<?php
/**
 * This class allows you to retrieve issuers.
 */
class Mollie_Api_Methods extends Mollie_Api_Base
{
	const RESOURCE = "methods";

	/**
	 * @return Mollie_Api_Resource_Method
	 */
	protected function getResourceObject ()
	{
		return new Mollie_Api_Resource_Method();
	}

	/**
	 * Get all enabled payment methods from Mollie.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @throws Mollie_Api_Exception
	 * @return Mollie_Api_Resource_Method[]|Mollie_Api_Resource_List
	 */
	public function all ($offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		return parent::all($offset, $limit);
	}
}