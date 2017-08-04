<?php

return [

    /**
     * ----------------------------------------------------------------
     * Default OAuth Provider
     * ----------------------------------------------------------------
     *
     * Here you may specify which of the providers below you wish to use as your default.
     */

    'default' => env('OAUTH_PROVIDER', 'Facebook'),

    /**
     * ----------------------------------------------------------------
     * OAuth Providers
     * ----------------------------------------------------------------
     *
     * Here are each of the authentication setup for your application.
     */

    'providers' => [

        // Visit https://www.dropbox.com/developers to create a Dropbox account.
        'dropbox' => [
            'driver'        => 'Dropbox',
            'client_id'     => 'Your_Dropbox_App_Key',
            'client_secret' => env('DROPBOX_APP_SECRET'),
            'redirect_to'   => env('DROPBOX_REDIRECT_URI'),
        ],

        // Visit https://developers.facebook.com/apps to create a Facebook account.
        // Reference: https://developers.facebook.com/docs/graph-api/using-graph-api/
        'facebook' => [
            'driver'        => 'Facebook',
            'client_id'     => 'Your_Facebook_App_ID',
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'redirect_to'   => env('FACEBOOK_REDIRECT_URI'),
            'user_agent'    => 'Your Apllication Name',
        ],

        // Visit https://github.com/settings/developers to create a GitHub account.
        'github' => [
            'driver'        => 'GitHub',
            'client_id'     => 'Your_Github_Client_Id',
            'client_secret' => env('GITHUB_CLIENT_SECRET'),
            'redirect_to'   => env('GITHUB_REDIRECT_URI'),
            'user_agent'    => 'Your Apllication Name',
        ],

    ],

    /**
     * ----------------------------------------------------------------
     * The Default User Role
     * ----------------------------------------------------------------
     *
     * This role is used when the user has authenticated via OAuth Provider.
     */

    'role' => 'user',

];
