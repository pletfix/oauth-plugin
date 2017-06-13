<?php

$di = \Core\Services\DI::getInstance();

$di->set('oauth-factory', \Pletfix\OAuth\Services\OAuthFactory::class, true);
