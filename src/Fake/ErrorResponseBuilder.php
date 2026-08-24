<?php

declare(strict_types=1);

namespace Mollie\Api\Fake;

class ErrorResponseBuilder
{
    protected int $status;

    protected string $title;

    protected string $detail;

    protected ?string $field;

    public function __construct(
        int $status,
        string $title,
        string $detail,
        ?string $field = null
    ) {
        $this->status = $status;
        $this->title = $title;
        $this->detail = $detail;
        $this->field = $field;
    }

    public function create(): MockResponse
    {
        $contents = json_decode(FakeResponseLoader::load('error'), true, flags: JSON_THROW_ON_ERROR);
        $contents['status'] = $this->status;
        $contents['title'] = $this->title;
        $contents['detail'] = $this->detail;
        $contents['field'] = $this->field;

        if (empty($this->field)) {
            unset($contents['field']);
        }

        return new MockResponse($contents, $this->status);
    }
}
