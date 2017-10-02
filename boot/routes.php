<?php

$router = Core\Application::router();

// Authentication OAuth Routes
$router->get('oauth/{provider}/login',  'OAuthController@login');
$router->post('oauth/{provider}/login', 'OAuthController@login');
$router->post('oauth/logout',           'OAuthController@logout', 'Auth');
