<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\ApprovalPrompt;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class ClientLink extends BaseResource
{

    public string $id;

    /**
     * @var \stdClass
     */
    public $_links;

    public function getRedirectUrl(string $client_id, string $state, array $scopes = [], string $approval_prompt = ApprovalPrompt::Auto->value): string
    {
        if (! in_array($approval_prompt, [ApprovalPrompt::Auto->value, ApprovalPrompt::Force->value])) {
            throw new \Exception('Invalid approval_prompt. Please use "auto" or "force".');
        }

        $query = http_build_query([
            'client_id' => $client_id,
            'state' => $state,
            'approval_prompt' => $approval_prompt,
            'scope' => implode(' ', $scopes),
        ], '', '&', PHP_QUERY_RFC3986);

        return "{$this->_links->clientLink->href}?{$query}";
    }
}
