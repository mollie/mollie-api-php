<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Resources\Partner;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\Route;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Types\SalesInvoiceStatus;
use Mollie\Api\Types\TerminalStatus;
use Mollie\Api\Types\WebhookStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Contract pin: mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902 (2026-08-26).
 * One test per resource: the spec-required minimum hydrates, every typed property
 * reads, and every spec-nullable or spec-absent field accepts null or stays null.
 */
class ContractNullabilityTest extends TestCase
{
    #[Test]
    public function terminal_nullable_and_absent_fields(): void
    {
        // entity-terminal.required is at specs.yaml:36837-36852. brand and model reference nullable schemas
        // at 36811-36826; serialNumber is ["string", "null"] at 36882-36887. timezone and locale are absent.
        $terminal = $this->hydrate(Terminal::class, [
            'resource' => 'terminal',
            'id' => 'term_7MgL4wea46qkRcoTZjWEH',
            'mode' => 'live',
            'description' => 'Terminal #12345',
            'status' => 'active',
            'brand' => null,
            'model' => null,
            'serialNumber' => null,
            'currency' => 'EUR',
            'profileId' => 'pfl_QkEhN94Ba',
            'createdAt' => '2022-02-12T11:58:35.0Z',
            'updatedAt' => '2022-11-15T13:32:11.0Z',
            '_links' => [],
        ]);

        $this->assertSame('term_7MgL4wea46qkRcoTZjWEH', $terminal->id);
        $this->assertSame('pfl_QkEhN94Ba', $terminal->profileId);
        $this->assertSame(TerminalStatus::Active, $terminal->status);
        $this->assertNull($terminal->brand);
        $this->assertNull($terminal->model);
        $this->assertNull($terminal->serialNumber);
        $this->assertSame('EUR', $terminal->currency);
        $this->assertSame('Terminal #12345', $terminal->description);
        $this->assertNull($terminal->timezone);
        $this->assertNull($terminal->locale);
        $this->assertSame('2022-02-12T11:58:35.0Z', $terminal->createdAt);
        $this->assertSame('2022-11-15T13:32:11.0Z', $terminal->updatedAt);
        $this->assertNull($terminal->disabledAt);
        $this->assertNull($terminal->activatedAt);
    }

    #[Test]
    public function capture_amount_accepts_null(): void
    {
        // capture-response requires amount at specs.yaml:32605-32616; entity-capture-response references
        // amount-nullable for that field at 42708-42709.
        $capture = $this->hydrate(Capture::class, [
            'resource' => 'capture',
            'id' => 'cpt_4qqhO89gsT',
            'mode' => 'live',
            'paymentId' => 'tr_7UhSN1zuXS',
            'amount' => null,
            'createdAt' => '2023-08-02T09:29:56.0Z',
        ]);

        $this->assertSame('cpt_4qqhO89gsT', $capture->id);
        $this->assertSame('live', $capture->mode);
        $this->assertNull($capture->amount);
        $this->assertNull($capture->description);
        $this->assertNull($capture->status);
        $this->assertNull($capture->settlementAmount);
        $this->assertSame('tr_7UhSN1zuXS', $capture->paymentId);
        $this->assertNull($capture->shipmentId);
        $this->assertNull($capture->settlementId);
        $this->assertSame('2023-08-02T09:29:56.0Z', $capture->createdAt);
    }

    #[Test]
    public function payment_link_profile_id_accepts_null(): void
    {
        // payment-link-response requires profileId at specs.yaml:34320-34335; profileId is nullable at 34218-34228.
        $link = $this->hydrate(PaymentLink::class, [
            'resource' => 'payment-link',
            'id' => 'pl_4Y0eZitmBnQ6IDoMqZQKh',
            'mode' => 'live',
            'description' => 'Bicycle tires',
            'profileId' => null,
            'archived' => false,
        ]);

        $this->assertSame('pl_4Y0eZitmBnQ6IDoMqZQKh', $link->id);
        $this->assertSame('live', $link->mode);
        $this->assertNull($link->profileId);
        $this->assertSame('Bicycle tires', $link->description);
        $this->assertFalse($link->archived);
        $this->assertNull($link->amount);
        $this->assertNull($link->minimumAmount);
        $this->assertNull($link->lines);
        $this->assertNull($link->billingAddress);
        $this->assertNull($link->shippingAddress);
        $this->assertNull($link->redirectUrl);
        $this->assertNull($link->webhookUrl);
        $this->assertNull($link->createdAt);
        $this->assertNull($link->paidAt);
        $this->assertNull($link->updatedAt);
        $this->assertNull($link->expiresAt);
    }

    #[Test]
    public function webhook_profile_id_accepts_null(): void
    {
        // entity-webhook.required includes profileId at specs.yaml:33898-33910; profileId is nullable at 33928-33933.
        $webhook = $this->hydrate(Webhook::class, [
            'resource' => 'webhook',
            'id' => 'wh_4KgGJJSZpH',
            'url' => 'https://example.com/webhook',
            'profileId' => null,
            'createdAt' => '2023-12-25T10:30:54+00:00',
            'name' => 'My webhook',
            'eventTypes' => ['payment-link.paid'],
            'status' => 'enabled',
            'mode' => 'live',
            '_links' => [],
        ]);

        $this->assertSame('wh_4KgGJJSZpH', $webhook->id);
        $this->assertSame('https://example.com/webhook', $webhook->url);
        $this->assertNull($webhook->profileId);
        $this->assertSame('2023-12-25T10:30:54+00:00', $webhook->createdAt);
        $this->assertSame('My webhook', $webhook->name);
        $this->assertSame(['payment-link.paid'], $webhook->eventTypes);
        $this->assertSame(WebhookStatus::Enabled, $webhook->status);
        $this->assertNull($webhook->webhookSecret);
        $this->assertSame('live', $webhook->mode);
    }

    #[Test]
    public function partner_type_accepts_null_and_optional_flags_default(): void
    {
        // GET /v2/organizations/me/partner requires resource and partnerType at specs.yaml:5597-5600.
        // partnerType is nullable at 5609-5615; partnerContractUpdateAvailable is optional at 5661-5665.
        $partner = $this->hydrate(Partner::class, ['resource' => 'partner', 'partnerType' => null]);

        $this->assertNull($partner->partnerType);
        $this->assertNull($partner->isCommissionPartner);
        $this->assertNull($partner->userAgentTokens);
        $this->assertNull($partner->partnerContractSignedAt);
        $this->assertNull($partner->partnerContractUpdateAvailable);
        $this->assertNull($partner->partnerContractExpiresAt);
    }

    #[Test]
    public function balance_transaction_deductions_accept_null_and_mode_defaults(): void
    {
        // entity-balance-transaction.required is at specs.yaml:29587-29595; deductions references amount-nullable
        // at 29630-29631. mode does not occur in entity-balance-transaction (29587-30097).
        $transaction = $this->hydrate(BalanceTransaction::class, [
            'resource' => 'balance-transaction',
            'id' => 'baltr_QM24QwzUWR4ev4Xfgyt29A',
            'type' => 'refund',
            'resultAmount' => ['currency' => 'EUR', 'value' => '-10.25'],
            'initialAmount' => ['currency' => 'EUR', 'value' => '-10.00'],
            'deductions' => null,
            'createdAt' => '2021-01-10T12:06:28+00:00',
        ]);

        $this->assertNull($transaction->mode);
        $this->assertSame('baltr_QM24QwzUWR4ev4Xfgyt29A', $transaction->id);
        $this->assertSame('refund', $transaction->type);
        $this->assertSame('2021-01-10T12:06:28+00:00', $transaction->createdAt);
        $this->assertInstanceOf(Money::class, $transaction->resultAmount);
        $this->assertInstanceOf(Money::class, $transaction->initialAmount);
        $this->assertNull($transaction->deductions);
    }

    #[Test]
    public function route_release_date_defaults_when_absent(): void
    {
        // entity-route has no required array and no releaseDate property at specs.yaml:37040-37104.
        $route = $this->hydrate(Route::class, [
            'resource' => 'route',
            'id' => 'rt_9dk4al1n',
            'paymentId' => 'tr_7UhSN1zuXS',
            'amount' => ['value' => '7.50', 'currency' => 'EUR'],
        ]);

        $this->assertSame('rt_9dk4al1n', $route->id);
        $this->assertSame('tr_7UhSN1zuXS', $route->paymentId);
        $this->assertInstanceOf(Money::class, $route->amount);
        $this->assertNull($route->releaseDate);
    }

    #[Test]
    public function connect_balance_transfer_category_defaults_when_absent(): void
    {
        // entity-balance-transfer-response.required omits category at specs.yaml:44114-44124;
        // the optional category property is defined at 44088-44089.
        $transfer = $this->hydrate(ConnectBalanceTransfer::class, [
            'resource' => 'connect-balance-transfer',
            'id' => 'cbt_4KgGJJSZpH',
            'amount' => ['currency' => 'EUR', 'value' => '100.00'],
            'source' => ['type' => 'organization', 'id' => 'org_12345678', 'description' => 'A'],
            'destination' => ['type' => 'organization', 'id' => 'org_87654321', 'description' => 'B'],
            'description' => 'Transfer',
            'status' => 'succeeded',
            'statusReason' => null,
            'createdAt' => '2023-12-25T10:30:54+00:00',
            'mode' => 'live',
        ]);

        $this->assertSame('cbt_4KgGJJSZpH', $transfer->id);
        $this->assertInstanceOf(Money::class, $transfer->amount);
        $this->assertSame('Transfer', $transfer->description);
        $this->assertSame('succeeded', $transfer->status);
        $this->assertNull($transfer->statusReason);
        $this->assertNull($transfer->category);
        $this->assertNull($transfer->executedAt);
        $this->assertSame('2023-12-25T10:30:54+00:00', $transfer->createdAt);
    }

    #[Test]
    public function sales_invoice_nullable_and_absent_fields(): void
    {
        // sales-invoice-response requires resource, id and mode at specs.yaml:35010-35013. In the response entity,
        // paymentTerm is nullable at 43699-43702, lines is nullable at 43571-43574, and currency/webhookUrl are absent.
        $invoice = $this->hydrate(SalesInvoice::class, [
            'resource' => 'sales-invoice',
            'id' => 'invoice_4Y0eZitmBnQ6IDoMqZQKh',
            'status' => 'draft',
            'vatScheme' => 'standard',
            'vatMode' => 'exclusive',
            'paymentTerm' => null,
            'recipientIdentifier' => '123532354',
            'lines' => null,
            'isEInvoice' => false,
            'amountDue' => ['value' => '0.00', 'currency' => 'EUR'],
            'subtotalAmount' => ['value' => '0.00', 'currency' => 'EUR'],
            'totalAmount' => ['value' => '0.00', 'currency' => 'EUR'],
            'totalVatAmount' => ['value' => '0.00', 'currency' => 'EUR'],
            'discountedSubtotalAmount' => ['value' => '0.00', 'currency' => 'EUR'],
            'createdAt' => '2024-10-03T10:47:38+00:00',
        ]);

        $this->assertSame('invoice_4Y0eZitmBnQ6IDoMqZQKh', $invoice->id);
        $this->assertNull($invoice->profileId);
        $this->assertNull($invoice->invoiceNumber);
        $this->assertNull($invoice->currency);
        $this->assertSame(SalesInvoiceStatus::Draft, $invoice->status);
        $this->assertSame('standard', $invoice->vatScheme);
        $this->assertSame('exclusive', $invoice->vatMode);
        $this->assertNull($invoice->memo);
        $this->assertNull($invoice->paymentTerm);
        $this->assertSame('123532354', $invoice->recipientIdentifier);
        $this->assertNull($invoice->lines);
        $this->assertNull($invoice->webhookUrl);
        $this->assertFalse($invoice->isEInvoice);
        $this->assertInstanceOf(Money::class, $invoice->amountDue);
        $this->assertInstanceOf(Money::class, $invoice->discountedSubtotalAmount);
        $this->assertSame('2024-10-03T10:47:38+00:00', $invoice->createdAt);
        $this->assertNull($invoice->issuedAt);
        $this->assertNull($invoice->dueAt);
    }

    /**
     * @template T of BaseResource
     *
     * @param  class-string<T>  $resourceClass
     * @param  array<string, mixed>  $data
     * @return T
     */
    private function hydrate(string $resourceClass, array $data): BaseResource
    {
        $resource = new $resourceClass($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($resource, $data, $this->createMock(Response::class));

        return $resource;
    }
}
