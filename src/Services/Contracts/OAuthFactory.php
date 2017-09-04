<?php

namespace Pletfix\OAuth\Services\Contracts;

interface OAuthFactory
{
    /**
     * Get the OAuth instance by given provider name.
     *
     * @param string|null $name
     * @return \Pletfix\OAuth\Services\Contracts\OAuth
     */
    public function provider($name = null);
}
