<?php

$route = \Core\Application::route();

// Authentication OAuth Routes
$route->get('oauth/{provider}/login',  'OAuthController@login');
$route->post('oauth/{provider}/login', 'OAuthController@login');
