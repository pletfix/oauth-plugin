<?php

$router = Core\Services\DI::getInstance()->get('router');

// Authentication OAuth Routes
$router->get('oauth/{provider}/login',  'OAuthController@login');
$router->post('oauth/{provider}/login', 'OAuthController@login');
$router->post('oauth/logout',           'OAuthController@logout', 'Auth');
