<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Types\ProfileStatus;

class ProfileEndpointTest extends BaseEndpointTest
{
    public function testGetProfile()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/profiles/pfl_ahe8z8OPut",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "resource": "profile",
                    "id": "pfl_ahe8z8OPut",
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
                            "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "chargebacks": {
                            "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "methods": {
                            "href": "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "payments": {
                            "href": "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "refunds": {
                            "href": "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "checkoutPreviewUrl": {
                            "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->apiClient->profiles->get('pfl_ahe8z8OPut');

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals("pfl_ahe8z8OPut", $profile->id);
        $this->assertEquals("live", $profile->mode);
        $this->assertEquals("My website name", $profile->name);
        $this->assertEquals("http://www.mywebsite.com", $profile->website);
        $this->assertEquals("info@mywebsite.com", $profile->email);
        $this->assertEquals("31123456789", $profile->phone);
        $this->assertEquals(5399, $profile->categoryCode);
        $this->assertEquals(ProfileStatus::VERIFIED, $profile->status);
        $this->assertEquals((object) ["status" => "pending"], $profile->review);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $profile->_links->self);

        $chargebacksLink = (object)["href" => "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($chargebacksLink, $profile->_links->chargebacks);

        $methodsLink = (object)["href" => "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($methodsLink, $profile->_links->methods);

        $paymentsLink = (object)["href" => "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($paymentsLink, $profile->_links->payments);

        $refundsLink = (object)["href" => "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($refundsLink, $profile->_links->refunds);

        $checkoutPreviewLink = (object)["href" => "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut", "type" => "text/html"];
        $this->assertEquals($checkoutPreviewLink, $profile->_links->checkoutPreviewUrl);
    }

    public function testGetProfileUsingMe()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/profiles/me",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "resource": "profile",
                    "id": "pfl_ahe8z8OPut",
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
                            "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "chargebacks": {
                            "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "methods": {
                            "href": "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "payments": {
                            "href": "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "refunds": {
                            "href": "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "checkoutPreviewUrl": {
                            "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->apiClient->profiles->get('me');

        $this->assertInstanceOf(CurrentProfile::class, $profile);
        $this->assertEquals("pfl_ahe8z8OPut", $profile->id);

        // No need to test it all again...
    }

    public function testGetCurrentProfile()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/profiles/me",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "resource": "profile",
                    "id": "pfl_ahe8z8OPut",
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
                            "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "chargebacks": {
                            "href": "https://api.mollie.com/v2/chargebacks",
                            "type": "application/hal+json"
                        },
                        "methods": {
                            "href": "https://api.mollie.com/v2/methods",
                            "type": "application/hal+json"
                        },
                        "payments": {
                            "href": "https://api.mollie.com/v2/payments",
                            "type": "application/hal+json"
                        },
                        "refunds": {
                            "href": "https://api.mollie.com/v2/refunds",
                            "type": "application/hal+json"
                        },
                        "checkoutPreviewUrl": {
                            "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->apiClient->profiles->getCurrent();

        $this->assertInstanceOf(CurrentProfile::class, $profile);
        $this->assertEquals("pfl_ahe8z8OPut", $profile->id);
        $this->assertEquals("live", $profile->mode);
        $this->assertEquals("My website name", $profile->name);
        $this->assertEquals("http://www.mywebsite.com", $profile->website);
        $this->assertEquals("info@mywebsite.com", $profile->email);
        $this->assertEquals("31123456789", $profile->phone);
        $this->assertEquals(5399, $profile->categoryCode);
        $this->assertEquals(ProfileStatus::VERIFIED, $profile->status);
        $this->assertEquals((object) ["status" => "pending"], $profile->review);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $profile->_links->self);

        $chargebacksLink = (object)["href" => "https://api.mollie.com/v2/chargebacks", "type" => "application/hal+json"];
        $this->assertEquals($chargebacksLink, $profile->_links->chargebacks);

        $methodsLink = (object)["href" => "https://api.mollie.com/v2/methods", "type" => "application/hal+json"];
        $this->assertEquals($methodsLink, $profile->_links->methods);

        $paymentsLink = (object)["href" => "https://api.mollie.com/v2/payments", "type" => "application/hal+json"];
        $this->assertEquals($paymentsLink, $profile->_links->payments);

        $refundsLink = (object)["href" => "https://api.mollie.com/v2/refunds", "type" => "application/hal+json"];
        $this->assertEquals($refundsLink, $profile->_links->refunds);

        $checkoutPreviewLink = (object)["href" => "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut", "type" => "text/html"];
        $this->assertEquals($checkoutPreviewLink, $profile->_links->checkoutPreviewUrl);
    }

    public function testListProfiles()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/profiles",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "profiles": [{
                                "resource": "profile",
                                "id": "pfl_ahe8z8OPut",
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
                                        "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                                        "type": "application/hal+json"
                                    },
                                    "chargebacks": {
                                        "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut",
                                        "type": "application/hal+json"
                                    },
                                    "methods": {
                                        "href": "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut",
                                        "type": "application/hal+json"
                                    },
                                    "payments": {
                                        "href": "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut",
                                        "type": "application/hal+json"
                                    },
                                    "refunds": {
                                        "href": "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut",
                                        "type": "application/hal+json"
                                    },
                                    "checkoutPreviewUrl": {
                                        "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                                        "type": "text/html"
                                    }
                                }
                            },
                            {
                                "resource": "profile",
                                "id": "pfl_znNaTRkJs5",
                                "mode": "live",
                                "name": "My website name 2",
                                "website": "http://www.mywebsite2.com",
                                "email": "info@mywebsite2.com",
                                "phone": "31123456789",
                                "categoryCode": 5399,
                                "status": "verified",
                                "review": {
                                    "status": "pending"
                                },
                                "createdAt": "2016-01-11T13:03:55+00:00",
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/profiles/pfl_znNaTRkJs5",
                                        "type": "application/hal+json"
                                    },
                                    "chargebacks": {
                                        "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_znNaTRkJs5",
                                        "type": "application/hal+json"
                                    },
                                    "methods": {
                                        "href": "https://api.mollie.com/v2/methods?profileId=pfl_znNaTRkJs5",
                                        "type": "application/hal+json"
                                    },
                                    "payments": {
                                        "href": "https://api.mollie.com/v2/payments?profileId=pfl_znNaTRkJs5",
                                        "type": "application/hal+json"
                                    },
                                    "refunds": {
                                        "href": "https://api.mollie.com/v2/refunds?profileId=pfl_znNaTRkJs5",
                                        "type": "application/hal+json"
                                    },
                                    "checkoutPreviewUrl": {
                                        "href": "https://www.mollie.com/payscreen/preview/pfl_znNaTRkJs5",
                                        "type": "text/html"
                                    }
                                }
                            }
                        ]
                    },
                    "count": 2,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/profiles-api/list-profiles",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.nl/v2/profiles?limit=50",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null
                    }
                }'
            )
        );

        $profiles = $this->apiClient->profiles->page();
        $this->assertInstanceOf(ProfileCollection::class, $profiles);
        $this->assertEquals(2, $profiles->count());

        foreach ($profiles as $profile) {
            $this->assertInstanceOf(Profile::class, $profile);
        }

        $selfLink = (object)["href" => "https://api.mollie.nl/v2/profiles?limit=50", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $profiles->_links->self);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/profiles-api/list-profiles", "type" => "text/html"];
        $this->assertEquals($documentationLink, $profiles->_links->documentation);
    }

    public function testUpdateProfile()
    {
        $expectedWebsiteName = 'Mollie';
        $expectedEmail = 'mollie@mollie.com';
        $expectedPhone = '31123456766';

        $this->mockApiCall(
            new Request('PATCH', '/v2/profiles/pfl_ahe8z8OPut'),
            new Response(
                200,
                [],
                '{
                    "resource": "profile",
                    "id": "pfl_ahe8z8OPut",
                    "mode": "live",
                    "name": "' . $expectedWebsiteName . '",
                    "website": "http://www.mywebsite.com",
                    "email": "' . $expectedEmail . '",
                    "phone": "' . $expectedPhone . '",
                    "categoryCode": 5399,
                    "status": "verified",
                    "review": {
                        "status": "pending"
                    },
                    "createdAt": "2016-01-11T13:03:55+00:00",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "chargebacks": {
                            "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "methods": {
                            "href": "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "payments": {
                            "href": "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "refunds": {
                            "href": "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut",
                            "type": "application/hal+json"
                        },
                        "checkoutPreviewUrl": {
                            "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $profile = $this->getProfile();
        $profile->name = $expectedWebsiteName;
        $profile->email = $expectedEmail;
        $profile->phone = $expectedPhone;

        $updatedProfile = $profile->update();

        $this->assertEquals($expectedWebsiteName, $updatedProfile->name);
        $this->assertEquals($expectedEmail, $updatedProfile->email);
        $this->assertEquals($expectedPhone, $updatedProfile->phone);
    }

    /**
     * @return Profile
     */
    private function getProfile()
    {
        $json = '{
            "resource": "profile",
            "id": "pfl_ahe8z8OPut",
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
                    "href": "https://api.mollie.com/v2/profiles/pfl_ahe8z8OPut",
                    "type": "application/hal+json"
                },
                "chargebacks": {
                    "href": "https://api.mollie.com/v2/chargebacks?profileId=pfl_ahe8z8OPut",
                    "type": "application/hal+json"
                },
                "methods": {
                    "href": "https://api.mollie.com/v2/methods?profileId=pfl_ahe8z8OPut",
                    "type": "application/hal+json"
                },
                "payments": {
                    "href": "https://api.mollie.com/v2/payments?profileId=pfl_ahe8z8OPut",
                    "type": "application/hal+json"
                },
                "refunds": {
                    "href": "https://api.mollie.com/v2/refunds?profileId=pfl_ahe8z8OPut",
                    "type": "application/hal+json"
                },
                "checkoutPreviewUrl": {
                    "href": "https://www.mollie.com/payscreen/preview/pfl_ahe8z8OPut",
                    "type": "text/html"
                }
            }
        }';

        return $this->copy(json_decode($json), new Profile($this->apiClient));
    }
}
