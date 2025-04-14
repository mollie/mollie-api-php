<?php

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
        $this->detail = addslashes($detail);
        $this->field = $field;
    }

    public function create(): MockResponse
    {
        $contents = FakeResponseLoader::load('error');

        $contents = str_replace([
            '{{ CODE }}',
            '{{ TITLE }}',
            '{{ DETAIL }}',
            '{{ FIELD }}',
        ], [
            (string) $this->status,
            $this->title,
            $this->detail,
            $this->field,
        ], $contents);

        $contents = json_decode($contents, true);

        if (empty($this->field)) {
            unset($contents['field']);
        }

        return new MockResponse($contents, $this->status);
    }
}
