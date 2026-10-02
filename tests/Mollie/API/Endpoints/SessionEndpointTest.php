<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;
use Mollie\Api\Types\SessionStatus;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class SessionEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testCreateSession()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/sessions",
                [],
                '{
                    "amount": {
                        "value": "10.00",
                        "currency": "EUR"
                    },
                    "description": "Order #12345",
                    "redirectUrl": "https://example.org/redirect",
                    "lines": [
                        {
                            "description": "Product A",
                            "quantity": 1,
                            "unitPrice": {
                                "currency": "EUR",
                                "value": "10.00"
                            },
                            "totalAmount": {
                                "currency": "EUR",
                                "value": "10.00"
                            }
                        }
                    ],
                    "requiredCustomerDetails": [
                        "email",
                        "billing-address"
                    ],
                    "payment": {
                        "webhookUrl": "https://example.org/webhook"
                    }
                }'
            ),
            new Response(
                201,
                [],
                $this->getSessionResponseFixture("sess_pbjz8x")
            )
        );

        $session = $this->apiClient->sessions->create([
            "amount" => [
                "value" => "10.00",
                "currency" => "EUR",
            ],
            "description" => "Order #12345",
            "redirectUrl" => "https://example.org/redirect",
            "lines" => [
                [
                    "description" => "Product A",
                    "quantity" => 1,
                    "unitPrice" => [
                        "currency" => "EUR",
                        "value" => "10.00",
                    ],
                    "totalAmount" => [
                        "currency" => "EUR",
                        "value" => "10.00",
                    ],
                ],
            ],
            "requiredCustomerDetails" => [
                "email",
                "billing-address",
            ],
            "payment" => [
                "webhookUrl" => "https://example.org/webhook",
            ],
        ]);

        $this->assertSession($session, 'sess_pbjz8x');
    }

    public function testGetSession()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/sessions/sess_pbjz8x"
            ),
            new Response(
                200,
                [],
                $this->getSessionResponseFixture("sess_pbjz8x")
            )
        );

        $session = $this->apiClient->sessions->get('sess_pbjz8x');

        $this->assertSession($session, 'sess_pbjz8x');
    }

    public function testListSessions()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/sessions"),
            new Response(
                200,
                [],
                '{
                   "count": 3,
                   "_embedded": {
                       "sessions": [
                           ' . $this->getSessionResponseFixture("sess_pbjz1x") . ',
                           ' . $this->getSessionResponseFixture("sess_pbjz2y") . ',
                           ' . $this->getSessionResponseFixture("sess_pbjz3z") . '
                       ]
                   },
                   "_links": {
                       "self": {
                           "href": "https://api.mollie.com/v2/sessions",
                           "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": {
                           "href": "https://api.mollie.com/v2/sessions?from=sess_stTC2WHAuS",
                           "type": "application/hal+json"
                       },
                       "documentation": {
                           "href": "https://docs.mollie.com/reference/v2/sessions-api/list-sessions",
                           "type": "text/html"
                       }
                   }
               }'
            )
        );

        $sessions = $this->apiClient->sessions->page();

        $this->assertInstanceOf(SessionCollection::class, $sessions);
        $this->assertEquals(3, $sessions->count);
        $this->assertEquals(3, count($sessions));

        $this->assertNull($sessions->_links->previous);
        $selfLink = $this->createLinkObject(
            "https://api.mollie.com/v2/sessions",
            "application/hal+json"
        );
        $this->assertEquals($selfLink, $sessions->_links->self);

        $nextLink = $this->createLinkObject(
            "https://api.mollie.com/v2/sessions?from=sess_stTC2WHAuS",
            "application/hal+json"
        );
        $this->assertEquals($nextLink, $sessions->_links->next);

        $documentationLink = $this->createLinkObject(
            "https://docs.mollie.com/reference/v2/sessions-api/list-sessions",
            "text/html"
        );
        $this->assertEquals($documentationLink, $sessions->_links->documentation);

        $this->assertSession($sessions[0], 'sess_pbjz1x');
        $this->assertSession($sessions[1], 'sess_pbjz2y');
        $this->assertSession($sessions[2], 'sess_pbjz3z');
    }

    public function testIterateSessions()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/sessions"),
            new Response(
                200,
                [],
                '{
                   "count": 3,
                   "_embedded": {
                       "sessions": [
                           ' . $this->getSessionResponseFixture("sess_pbjz1x") . ',
                           ' . $this->getSessionResponseFixture("sess_pbjz2y") . ',
                           ' . $this->getSessionResponseFixture("sess_pbjz3z") . '
                       ]
                   },
                   "_links": {
                       "self": {
                           "href": "https://api.mollie.com/v2/sessions",
                           "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": null,
                       "documentation": {
                           "href": "https://docs.mollie.com/reference/v2/sessions-api/list-sessions",
                           "type": "text/html"
                       }
                   }
               }'
            )
        );

        foreach ($this->apiClient->sessions->iterator() as $session) {
            $this->assertInstanceOf(Session::class, $session);
        }
    }

    public function testCancelSession()
    {
        $this->mockApiCall(
            new Request("DELETE", "/v2/sessions/sess_pbjz1x"),
            new Response(
                200,
                [],
                $this->getSessionResponseFixture(
                    'sess_pbjz1x',
                    SessionStatus::STATUS_EXPIRED
                )
            )
        );
        $session = $this->apiClient->sessions->cancel('sess_pbjz1x');
        $this->assertSession($session, 'sess_pbjz1x', SessionStatus::STATUS_EXPIRED);
    }

    /** @test */
    public function testUpdateSession()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/sessions/sess_pbjz8x",
                [],
                '{
                    "billingAddress": {
                        "organizationName": "Organization Name LTD.",
                        "streetAndNumber": "Keizersgracht 313",
                        "postalCode": "1234AB",
                        "city": "Amsterdam",
                        "country": "NL",
                        "givenName": "Piet",
                        "familyName": "Mondriaan",
                        "email": "piet@mondriaan.com",
                        "region": "Noord-Holland",
                        "title": "Dhr",
                        "phone": "+31208202070"
                    },
                    "shippingAddress": {
                        "organizationName": "Organization Name LTD.",
                        "streetAndNumber": "Keizersgracht 313",
                        "postalCode": "1016 EE",
                        "city": "Amsterdam",
                        "country": "nl",
                        "givenName": "Luke",
                        "familyName": "Skywalker",
                        "email": "luke@skywalker.com"
                    }
                }'
            ),
            new Response(
                200,
                [],
                $this->getSessionResponseFixture(
                    "sess_pbjz8x",
                    SessionStatus::STATUS_OPEN
                )
            )
        );

        $sessionJSON = $this->getSessionResponseFixture('sess_pbjz8x');

        /** @var Session $session */
        $session = $this->copy(json_decode($sessionJSON), new Session($this->apiClient));

        $session->billingAddress->organizationName = "Organization Name LTD.";
        $session->billingAddress->streetAndNumber = "Keizersgracht 313";
        $session->billingAddress->city = "Amsterdam";
        $session->billingAddress->region = "Noord-Holland";
        $session->billingAddress->postalCode = "1234AB";
        $session->billingAddress->country = "NL";
        $session->billingAddress->title = "Dhr";
        $session->billingAddress->givenName = "Piet";
        $session->billingAddress->familyName = "Mondriaan";
        $session->billingAddress->email = "piet@mondriaan.com";
        $session->billingAddress->phone = "+31208202070";
        $session = $session->update();

        $this->assertSession($session, "sess_pbjz8x", SessionStatus::STATUS_OPEN);
    }

    protected function assertSession($session, $session_id, $sessionStatus = SessionStatus::STATUS_OPEN)
    {
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('session', $session->resource);
        $this->assertEquals($session_id, $session->id);

        $this->assertAmountObject('10.00', 'EUR', $session->amount);

        $this->assertEquals($sessionStatus, $session->status);
        $this->assertEquals($sessionStatus === SessionStatus::STATUS_OPEN, $session->isOpen());
        $this->assertEquals($sessionStatus === SessionStatus::STATUS_EXPIRED, $session->isExpired());
        $this->assertEquals($sessionStatus === SessionStatus::STATUS_COMPLETED, $session->isCompleted());

        $this->assertEquals("dddiweodh23973yo23d2h...", $session->clientAccessToken);
        $this->assertEquals("Order #12345", $session->description);
        $this->assertEquals("https://example.org/redirect", $session->redirectUrl);
        $this->assertEquals(["email", "billing-address"], $session->requiredCustomerDetails);
        $this->assertEquals("pfl_QkEhN94Ba", $session->profileId);
        $this->assertEquals("2026-09-30T12:00:00+00:00", $session->createdAt);
        $this->assertEquals(
            $sessionStatus === SessionStatus::STATUS_EXPIRED ? "2026-09-30T12:30:00+00:00" : null,
            $session->expiredAt
        );
        $this->assertEquals(
            $sessionStatus === SessionStatus::STATUS_COMPLETED ? "2026-09-30T12:30:00+00:00" : null,
            $session->completedAt
        );
        $this->assertNull($session->getRedirectUrl());

        $links = (object)[
            'self' => $this->createLinkObject(
                'https://api.mollie.com/v2/sessions/' . $session_id,
                'application/hal+json'
            ),
        ];
        $this->assertEquals($links, $session->_links);
    }

    protected function getSessionResponseFixture($session_id, $sessionStatus = SessionStatus::STATUS_OPEN)
    {
        return str_replace(
            [
                "<<session_id>>",
                "<<session_status>>",
                "<<expired_at>>",
                "<<completed_at>>",
            ],
            [
                $session_id,
                $sessionStatus,
                $sessionStatus === SessionStatus::STATUS_EXPIRED ? '"2026-09-30T12:30:00+00:00"' : 'null',
                $sessionStatus === SessionStatus::STATUS_COMPLETED ? '"2026-09-30T12:30:00+00:00"' : 'null',
            ],
            '{
                "resource": "session",
                "id": "<<session_id>>",
                "status": "<<session_status>>",
                "mode": "live",
                "clientAccessToken": "dddiweodh23973yo23d2h...",
                "amount": {
                    "currency": "EUR",
                    "value": "10.00"
                },
                "description": "Order #12345",
                "redirectUrl": "https://example.org/redirect",
                "metadata": {
                    "orderId": "12345"
                },
                "payment": {
                    "webhookUrl": "https://example.org/webhook"
                },
                "requiredCustomerDetails": [
                    "email",
                    "billing-address"
                ],
                "profileId": "pfl_QkEhN94Ba",
                "createdAt": "2026-09-30T12:00:00+00:00",
                "expiredAt": <<expired_at>>,
                "completedAt": <<completed_at>>,
                "lines": [
                    {
                        "description": "Product A",
                        "quantity": 1,
                        "unitPrice": {
                            "currency": "EUR",
                            "value": "10.00"
                        },
                        "totalAmount": {
                            "currency": "EUR",
                            "value": "10.00"
                        }
                    }
                ],
                "billingAddress": {
                    "organizationName": "Organization Name LTD.",
                    "streetAndNumber": "Keizersgracht 313",
                    "postalCode": "1234AB",
                    "city": "Amsterdam",
                    "country": "NL",
                    "givenName": "Piet",
                    "familyName": "Mondriaan",
                    "email": "piet@mondriaan.com",
                    "region": "Noord-Holland",
                    "title": "Dhr",
                    "phone": "+31208202070"
                },
                "shippingAddress": {
                    "organizationName": "Organization Name LTD.",
                    "streetAndNumber": "Keizersgracht 313",
                    "postalCode": "1016 EE",
                    "city": "Amsterdam",
                    "country": "nl",
                    "givenName": "Luke",
                    "familyName": "Skywalker",
                    "email": "luke@skywalker.com"
                },
                "_links": {
                    "self": {
                        "href": "https://api.mollie.com/v2/sessions/<<session_id>>",
                        "type": "application/hal+json"
                    }
                }
            }'
        );
    }
}
