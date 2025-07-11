<?php

namespace Tests\Types;

use Mollie\Api\Types\WebhookEventType;
use PHPUnit\Framework\TestCase;

class WebhookEventTypeTest extends TestCase
{
    /** @test */
    public function webhook_event_types_have_correct_values()
    {
        $this->assertEquals('payment-link.paid', WebhookEventType::PAYMENT_LINK_PAID);
    }

    /** @test */
    public function get_all_returns_all_available_event_types()
    {
        $expectedEventTypes = [
            'payment-link.paid',
            'profile.created',
            'profile.verified',
            'profile.blocked',
            'profile.deleted',
        ];

        $this->assertEquals($expectedEventTypes, WebhookEventType::getAll());
    }
}
