<?php

namespace Pletfix\OAuth\Services\Contracts;

interface OAuthFactory
{
    /**
     * Get a OAuth instance by given provider name.
     *
     * @param string|null $name
     * @return \Core\Services\Contracts\OAuth
     */
    public function provider($name = null);
}
