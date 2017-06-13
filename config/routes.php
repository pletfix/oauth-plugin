<?php

$route = \Core\Application::route();

// Authentication OAuth Routes
$route->get('auth/oauth/{provider}',  'Auth\OAuthController@login');
$route->post('auth/oauth/{provider}', 'Auth\OAuthController@login');
