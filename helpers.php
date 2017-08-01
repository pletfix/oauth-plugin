<?php

use Core\Services\DI;

if (!function_exists('oauth')) {
    /**
     * Get the authentication object by given provider name.
     *
     * @param string|null $provider OAuth provider such as "twitter" or "facebook".
     * @return \Pletfix\OAuth\Services\Contracts\OAuth
     */
    function oauth($provider = null)
    {
        return DI::getInstance()->get('oauth-factory')->provider($provider);
    }
}
