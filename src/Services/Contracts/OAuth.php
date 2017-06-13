<?php

namespace Pletfix\OAuth\Services\Contracts;

use Response;

interface OAuth
{
    /**
     * Authorize the application through the OAuth provider.
     *
     * @return Response
     */
    public function authorize();

    /**
     * Get the authenticated account information.
     *
     * The return value is an array with following attributes:
     * - id    (string) The unique identifier of the account.
     * - name  (string) The display name of the user.
     * - email (string) The email address of the user.
     *
     * @return array
     */
    public function getAccount();

    /**
     * Set the access token.
     *
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken);

    /**
     * Get the access token.
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Determine if the access token exist.
     *
     * @return bool
     */
    public function hasAccessToken();
}
