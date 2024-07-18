<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\OrderLineCollection;
use Mollie\Api\Types\OrderLineStatus;
use Mollie\Api\Types\OrderLineType;
use Mollie\Api\Types\OrderStatus;
use stdClass;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class OrderTest extends \PHPUnit\Framework\TestCase
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;

    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestOrderStatuses
     */
    public function testOrderStatuses($status, $function, $expected_boolean)
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $order->status = $status;

        $this->assertEquals($expected_boolean, $order->{$function}());
    }

    public function dpTestOrderStatuses()
    {
        return [
            [OrderStatus::CREATED, "isCreated", true],
            [OrderStatus::CREATED, "isPaid", false],
            [OrderStatus::CREATED, "isAuthorized", false],
            [OrderStatus::CREATED, "isCanceled", false],
            [OrderStatus::CREATED, "isShipping", false],
            [OrderStatus::CREATED, "isCompleted", false],
            [OrderStatus::CREATED, "isExpired", false],
            [OrderStatus::CREATED, "isPending", false],

            [OrderStatus::PAID, "isCreated", false],
            [OrderStatus::PAID, "isPaid", true],
            [OrderStatus::PAID, "isAuthorized", false],
            [OrderStatus::PAID, "isCanceled", false],
            [OrderStatus::PAID, "isShipping", false],
            [OrderStatus::PAID, "isCompleted", false],
            [OrderStatus::PAID, "isExpired", false],
            [OrderStatus::PAID, "isPending", false],

            [OrderStatus::AUTHORIZED, "isCreated", false],
            [OrderStatus::AUTHORIZED, "isPaid", false],
            [OrderStatus::AUTHORIZED, "isAuthorized", true],
            [OrderStatus::AUTHORIZED, "isCanceled", false],
            [OrderStatus::AUTHORIZED, "isShipping", false],
            [OrderStatus::AUTHORIZED, "isCompleted", false],
            [OrderStatus::AUTHORIZED, "isExpired", false],
            [OrderStatus::AUTHORIZED, "isPending", false],

            [OrderStatus::CANCELED, "isCreated", false],
            [OrderStatus::CANCELED, "isPaid", false],
            [OrderStatus::CANCELED, "isAuthorized", false],
            [OrderStatus::CANCELED, "isCanceled", true],
            [OrderStatus::CANCELED, "isShipping", false],
            [OrderStatus::CANCELED, "isCompleted", false],
            [OrderStatus::CANCELED, "isExpired", false],
            [OrderStatus::CANCELED, "isPending", false],

            [OrderStatus::SHIPPING, "isCreated", false],
            [OrderStatus::SHIPPING, "isPaid", false],
            [OrderStatus::SHIPPING, "isAuthorized", false],
            [OrderStatus::SHIPPING, "isCanceled", false],
            [OrderStatus::SHIPPING, "isShipping", true],
            [OrderStatus::SHIPPING, "isCompleted", false],
            [OrderStatus::SHIPPING, "isExpired", false],
            [OrderStatus::SHIPPING, "isPending", false],

            [OrderStatus::COMPLETED, "isCreated", false],
            [OrderStatus::COMPLETED, "isPaid", false],
            [OrderStatus::COMPLETED, "isAuthorized", false],
            [OrderStatus::COMPLETED, "isCanceled", false],
            [OrderStatus::COMPLETED, "isShipping", false],
            [OrderStatus::COMPLETED, "isCompleted", true],
            [OrderStatus::COMPLETED, "isExpired", false],
            [OrderStatus::COMPLETED, "isPending", false],

            [OrderStatus::EXPIRED, "isCreated", false],
            [OrderStatus::EXPIRED, "isPaid", false],
            [OrderStatus::EXPIRED, "isAuthorized", false],
            [OrderStatus::EXPIRED, "isCanceled", false],
            [OrderStatus::EXPIRED, "isShipping", false],
            [OrderStatus::EXPIRED, "isCompleted", false],
            [OrderStatus::EXPIRED, "isExpired", true],
            [OrderStatus::EXPIRED, "isPending", false],

            [OrderStatus::PENDING, "isCreated", false],
            [OrderStatus::PENDING, "isPaid", false],
            [OrderStatus::PENDING, "isAuthorized", false],
            [OrderStatus::PENDING, "isCanceled", false],
            [OrderStatus::PENDING, "isShipping", false],
            [OrderStatus::PENDING, "isCompleted", false],
            [OrderStatus::PENDING, "isExpired", false],
            [OrderStatus::PENDING, "isPending", true],
        ];
    }

    public function testCanGetLinesAsResourcesOnOrderResource()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $orderLine = new stdClass;
        $lineArray = [
            'resource' => 'orderline',
            'id' => 'odl_dgtxyl',
            'orderId' => 'ord_pbjz8x',
            'type' => 'physical',
            'name' => 'LEGO 42083 Bugatti Chiron',
            'productUrl' => 'https://shop.lego.com/nl-NL/Bugatti-Chiron-42083',
            'imageUrl' => 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$',
            'sku' => '5702016116977',
            'status' => 'created',
            'quantity' => 2,
            'unitPrice' => (object) [
                'value' => '399.00',
                'currency' => 'EUR',
            ],
            'vatRate' => '21.00',
            'vatAmount' => (object) [
                'value' => '121.14',
                'currency' => 'EUR',
            ],
            'discountAmount' => (object) [
                'value' => '100.00',
                'currency' => 'EUR',
            ],
            'totalAmount' => (object) [
                'value' => '698.00',
                'currency' => 'EUR',
            ],
            'createdAt' => '2018-08-02T09:29:56+00:00',
        ];

        foreach ($lineArray as $key => $value) {
            $orderLine->{$key} = $value;
        }

        $order->lines = [$orderLine];

        $lines = $order->lines();

        $this->assertInstanceOf(OrderLineCollection::class, $lines);
        $this->assertCount(1, $lines);

        $line = $lines[0];

        $this->assertInstanceOf(OrderLine::class, $line);

        $this->assertEquals("orderline", $line->resource);
        $this->assertEquals("odl_dgtxyl", $line->id);
        $this->assertEquals('ord_pbjz8x', $line->orderId);
        $this->assertEquals("LEGO 42083 Bugatti Chiron", $line->name);
        $this->assertEquals("https://shop.lego.com/nl-NL/Bugatti-Chiron-42083", $line->productUrl);
        $this->assertEquals('https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$', $line->imageUrl);
        $this->assertEquals("5702016116977", $line->sku);
        $this->assertEquals(OrderLineType::PHYSICAL, $line->type);
        $this->assertEquals(OrderLineStatus::CREATED, $line->status);
        $this->assertEquals(2, $line->quantity);
        $this->assertAmountObject("399.00", "EUR", $line->unitPrice);
        $this->assertEquals("21.00", $line->vatRate);
        $this->assertAmountObject("121.14", "EUR", $line->vatAmount);
        $this->assertAmountObject("100.00", "EUR", $line->discountAmount);
        $this->assertAmountObject("698.00", "EUR", $line->totalAmount);
        $this->assertEquals("2018-08-02T09:29:56+00:00", $line->createdAt);
    }

    public function testCanGetPaymentsAsResourcesOnOrderResource()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $orderLine = new stdClass;
        $lineArray = [
            'resource' => 'orderline',
            'id' => 'odl_dgtxyl',
            'orderId' => 'ord_pbjz8x',
            'type' => 'physical',
            'name' => 'LEGO 42083 Bugatti Chiron',
            'productUrl' => 'https://shop.lego.com/nl-NL/Bugatti-Chiron-42083',
            'imageUrl' => 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$',
            'sku' => '5702016116977',
            'status' => 'created',
            'quantity' => 2,
            'unitPrice' => (object) [
                'value' => '399.00',
                'currency' => 'EUR',
            ],
            'vatRate' => '21.00',
            'vatAmount' => (object) [
                'value' => '121.14',
                'currency' => 'EUR',
            ],
            'discountAmount' => (object) [
                'value' => '100.00',
                'currency' => 'EUR',
            ],
            'totalAmount' => (object) [
                'value' => '698.00',
                'currency' => 'EUR',
            ],
            'createdAt' => '2018-08-02T09:29:56+00:00',
        ];

        foreach ($lineArray as $key => $value) {
            $orderLine->{$key} = $value;
        }

        $order->lines = [$orderLine];

        $lines = $order->lines();

        $this->assertInstanceOf(OrderLineCollection::class, $lines);
        $this->assertCount(1, $lines);

        $line = $lines[0];

        $this->assertInstanceOf(OrderLine::class, $line);

        $this->assertEquals("orderline", $line->resource);
        $this->assertEquals("odl_dgtxyl", $line->id);
        $this->assertEquals('ord_pbjz8x', $line->orderId);
        $this->assertEquals("LEGO 42083 Bugatti Chiron", $line->name);
        $this->assertEquals("https://shop.lego.com/nl-NL/Bugatti-Chiron-42083", $line->productUrl);
        $this->assertEquals('https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$', $line->imageUrl);
        $this->assertEquals("5702016116977", $line->sku);
        $this->assertEquals(OrderLineType::PHYSICAL, $line->type);
        $this->assertEquals(OrderLineStatus::CREATED, $line->status);
        $this->assertEquals(2, $line->quantity);
        $this->assertAmountObject("399.00", "EUR", $line->unitPrice);
        $this->assertEquals("21.00", $line->vatRate);
        $this->assertAmountObject("121.14", "EUR", $line->vatAmount);
        $this->assertAmountObject("100.00", "EUR", $line->discountAmount);
        $this->assertAmountObject("698.00", "EUR", $line->totalAmount);
        $this->assertEquals("2018-08-02T09:29:56+00:00", $line->createdAt);
    }

    public function testGetCheckoutUrlWorks()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $order->_links = $this->getOrderLinksDummy([
            'checkout' => (object) [
                'href' => 'https://www.some-mollie-checkout-url.com/123',
                'type' => 'text/html',
            ],
        ]);

        $this->assertEquals(
            'https://www.some-mollie-checkout-url.com/123',
            $order->getCheckoutUrl()
        );
    }

    public function testGetCheckoutUrlReturnsNullIfNoCheckoutUrlAvailable()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $order->_links = $this->getOrderLinksDummy(['checkout' => null]);

        $this->assertNull($order->getCheckoutUrl());
    }

    public function testPaymentsHelperReturnsIfNoEmbedAvailable()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $this->assertNull($order->payments());
    }

    public function testPaymentsHelperReturnsIfNoPaymentsEmbedded()
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $order->_embedded = null;
        $this->assertNull($order->payments());
    }

    protected function getOrderLinksDummy($overrides = [])
    {
        return (object) array_merge(
            [],
            $overrides
        );
    }
}
