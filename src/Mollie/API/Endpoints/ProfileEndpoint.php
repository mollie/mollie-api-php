<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

/**
 * Copyright (c) 2015, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Mollie B.V. <info@mollie.com>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.com
 *
 * @method Profile[]|ProfileCollection all($offset = 0, $limit = 0, array $filters = array())
 * @method Profile get($profile_id, array $filters = array())
 * @method Profile create(array $data = array(), array $filters = array())
 * @method Profile delete($profile_id0)
 */
class ProfileEndpoint extends EndpointAbstract
{

    protected $resource_path = "profiles";

    /**
     * @return Profile
     */
    protected function getResourceObject()
    {
        return new Profile();
    }

    /**
     * @param Profile $profile
     *
     * @return Profile
     */
    public function update(Profile $profile)
    {
        $body = json_encode(array(
            "name" => $profile->name,
            "website" => $profile->website,
            "email" => $profile->email,
            "phone" => $profile->phone,
            "categoryCode" => $profile->categoryCode,
            "mode" => $profile->mode
        ));

        /** @var Profile $updated_profile */
        $updated_profile = $this->rest_update($this->getResourcePath(), $profile->id, $body);

        return $updated_profile;
    }

    /**
     * @return BaseCollection
     */
    protected function getResourceCollectionObject()
    {
        return new ProfileCollection();
    }
}
