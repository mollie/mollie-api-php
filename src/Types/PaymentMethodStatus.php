<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

/**
 * Activation status of a payment method on a profile.
 *
 * A method that was never requested has no status: the API sends `null`,
 * which `Method::$status` carries through its own nullable type. There is
 * deliberately no "not requested" case.
 *
 * @link https://docs.mollie.com/reference/v2/methods-api/get-method#parameters
 */
enum PaymentMethodStatus: string
{
    /**
     * The payment method is activated and ready for use.
     */
    case Activated = 'activated';

    /**
     * Mollie is waiting for you to finish onboarding in the Merchant Dashboard before
     * the payment method can be activated.
     */
    case PendingBoarding = 'pending-boarding';

    /**
     * Mollie needs to review your request for this payment method before it can be activated.
     */
    case PendingReview = 'pending-review';

    /**
     * Activation of this payment method relies on you taking action with an external party,
     * for example signing up with PayPal or a giftcard issuer.
     */
    case PendingExternal = 'pending-external';

    /**
     * Your request for this payment method was rejected.
     * Whenever Mollie rejects such a request, you will always be informed via email.
     */
    case Rejected = 'rejected';
}
