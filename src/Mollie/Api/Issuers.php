<?php
/**
 * This class allows you to retrieve issuers.
 */
class Mollie_Api_Issuers extends Mollie_Api_Base
{
	const RESOURCE = "issuers";

	/**
	 * @return Mollie_Api_Resource_Payment
	 */
	protected function getResourceObject ()
	{
		return new Mollie_Api_Resource_Issuer();
	}

	/**
	 * Get all issuers from Mollie. Issuers can be used to specify which bank your customer wants to use to perform an
	 * iDEAL payment. As such, they only apply to iDEAL payments.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @throws Mollie_Api_Exception
	 * @return Mollie_Api_Resource_Issuer[]|Mollie_Api_Resource_List
	 */
	public function all ($offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		return parent::all($offset, $limit);
	}
}