<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\GetPaymentQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class GetPaymentRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\Payment::class;

    private string $id;

    private GetPaymentQuery $query;

    public function __construct(
        string $id,
        GetPaymentQuery $query,
    ) {
        $this->id = $id;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix(Payment::$resourceIdPrefix),
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
