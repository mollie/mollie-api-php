<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\Profile;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ProfileMethodEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testEnableProfileMethod()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact"
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "method",
                    "id": "bancontact",
                    "description": "Bancontact",
                    "image": {
                        "size1x": "https://www.mollie.com/external/icons/payment-methods/bancontact.png",
                        "size2x": "https://www.mollie.com/external/icons/payment-methods/bancontact%402x.png",
                        "svg": "https://www.mollie.com/external/icons/payment-methods/bancontact.svg"
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/methods/bancontact",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/profiles-api/activate-method",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->getProfile();
        $method = $profile->enableMethod('bancontact');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('bancontact', $method->id);
        $this->assertEquals('Bancontact', $method->description);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact.png', $method->image->size1x);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact%402x.png', $method->image->size2x);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact.svg', $method->image->svg);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/methods/bancontact",
            "application/hal+json",
            $method->_links->self
        );

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/profiles-api/activate-method",
            "text/html",
            $method->_links->documentation
        );
    }

    public function testDisableProfileMethod()
    {
        $this->mockApiCall(
            new Request(
                "DELETE",
                "/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact"
            ),
            new Response(204)
        );

        $profile = $this->getProfile();
        $result = $profile->disableMethod('bancontact');

        $this->assertNull($result);
    }

    public function testEnableCurrentProfileMethod()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/profiles/me/methods/bancontact"
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "method",
                    "id": "bancontact",
                    "description": "Bancontact",
                    "image": {
                        "size1x": "https://www.mollie.com/external/icons/payment-methods/bancontact.png",
                        "size2x": "https://www.mollie.com/external/icons/payment-methods/bancontact%402x.png",
                        "svg": "https://www.mollie.com/external/icons/payment-methods/bancontact.svg"
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/methods/bancontact",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/profiles-api/activate-method",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->getCurrentProfile();
        $method = $profile->enableMethod('bancontact');

        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('bancontact', $method->id);
        $this->assertEquals('Bancontact', $method->description);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact.png', $method->image->size1x);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact%402x.png', $method->image->size2x);
        $this->assertEquals('https://www.mollie.com/external/icons/payment-methods/bancontact.svg', $method->image->svg);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/methods/bancontact",
            "application/hal+json",
            $method->_links->self
        );

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/profiles-api/activate-method",
            "text/html",
            $method->_links->documentation
        );
    }

    public function testDisableCurrentProfileMethod()
    {
        $this->mockApiCall(
            new Request(
                "DELETE",
                "/v2/profiles/me/methods/bancontact"
            ),
            new Response(204)
        );

        $profile = $this->getCurrentProfile();

        $result = $profile->disableMethod('bancontact');

        $this->assertNull($result);
    }

    /**
     * @return CurrentProfile
     */
    private function getCurrentProfile()
    {
        return $this->copy(
            json_decode($this->getProfileFixture()),
            new CurrentProfile($this->apiClient)
        );
    }

    /**
     * @return Profile
     */
    private function getProfile()
    {
        return $this->copy(
            json_decode($this->getProfileFixture()),
            new Profile($this->apiClient)
        );
    }

    /**
     * @return string
     */
    private function getProfileFixture()
    {
        return '{
            "resource": "profile",
            "id": "pfl_v9hTwCvYqw",
            "mode": "live",
            "name": "My website name",
            "website": "http://www.mywebsite.com",
            "email": "info@mywebsite.com",
            "phone": "31123456789",
            "categoryCode": 5399,
            "status": "verified",
            "review": {
                "status": "pending"
            },
            "createdAt": "2016-01-11T13:03:55+00:00",
            "_links": {
                "self": {
                    "href": "https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw",
                    "type": "application/hal+json"
                },
                "chargebacks": {
                    "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_v9hTwCvYqw",
                    "type": "application/hal+json"
                },
                "methods": {
                    "href": "https://api.mollie.com/v2/methods?profileId=pfl_v9hTwCvYqw",
                    "type": "application/hal+json"
                },
                "payments": {
                    "href": "https://api.mollie.com/v2/payments?profileId=pfl_v9hTwCvYqw",
                    "type": "application/hal+json"
                },
                "refunds": {
                    "href": "https://api.mollie.com/v2/refunds?profileId=pfl_v9hTwCvYqw",
                    "type": "application/hal+json"
                },
                "checkoutPreviewUrl": {
                    "href": "https://www.mollie.com/payscreen/preview/pfl_v9hTwCvYqw",
                    "type": "text/html"
                }
            }
        }';
    }
}
