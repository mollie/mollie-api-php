<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum OnboardingStatus: string
{
    /**
     * The onboarding is not completed and the merchant needs to provide (more) information
     */
    case NeedsData = 'needs-data';

    /**
     * The merchant provided all information and Mollie needs to check this
     */
    case InReview = 'in-review';

    /**
     * The onboarding is completed
     */
    case Completed = 'completed';
}
