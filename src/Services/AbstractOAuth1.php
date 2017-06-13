<?php

namespace Pletfix\OAuth\Services;

use Pletfix\OAuth\Services\Contracts\OAuth as OAuthContract;

/**
 * TODO This class is not implemented yet.
 *
 * Examples for OAuth1 providers:
 *  - BitBucket
 *  - Flickr
 *  - Tumblr
 *  - Twitter
 *  - Xing
 *  - Yahoo
 */

//class Db extends AbstractAuth
abstract class AbstractOAuth1 implements OAuthContract
{
    /**
     * Connection Settings
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new Auth instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        throw new \Exception('Not implemented yet.');
    }
}