# OAuth Plugin for Pletfix

## About This

This plugin provides a OAuth service to authenticate the user through the social networking service provider such like 
Facebook or Dropbox.

Currently the following drivers are integrated:

- Dropbox
- Facebook
- GitHub
- Spotify

You are welcome when you make a pull request with other socialite drivers.

## Installation 

Fetch the package by running the following terminal command under the application's directory:

    composer require pletfix/oauth

After downloading, enter this command in your terminal to register the plugin:

    php console plugin pletfix/oauth 

## Environment and Configuration
    
Open the configuration file `./config/oauth.php` under the application's directory and override the defaults if you wish.
   
## Customize
        
If you like to use an another route path, have a look in the plugin's route entries in `./vendor/pletfix/oauth/config/routes.php`. 
You can override  or modify the route entries in the application's route file `./config/boot/routes.php` like you wish:

    $route->get('oauth/{provider}/login',  'OAuthController@login');
    $route->post('oauth/{provider}/login', 'OAuthController@login');
 
## Usage

### User Authentication

Enter the following URL into your Browser to redirect to the login screen on your OAuth provider:

    https://<your-application>/oauth/<provider>/login
    
You must replace the placeholder "<provider>" with one of the providers configured in the configuration file `config/oauth.php`,
for example with Facebook:
    
    https://<your-application>/oauth/facebook/login

#### User Model

If you have defined a user model in the configuration, the user attributes are stored in the database.
By default, the user model from the [Pletfix Application Skeleton](https://github.com/pletfix/app) is used and no 
further migration is required.

#### Logout

You may invoke just the following command to logout the user: 
 
    auth()->logout();

### OAuth Service

#### Accessing the OAuth service

You can get an instance of the OAuth Service from the Dependency Injector via the OAuth Factory:

    /** @var Pletfix\OAuth\Services\Contracts\OAuth $oauth */
    $oauth = DI::getInstance()->get('oauth-factory')->provider($provider);
    
You can also use the `oauth()` function to get the OAuth service, it is more comfortable:
       
    $oauth = oauth();

#### Available Methods

#### `authorize()`

Authorize the application through the OAuth provider.

    if (!$oauth->authorize()) {
        return redirect('', [], [
            'errors' => ['Forbidden!']
        ]);
    }
    
After authorization the access token is set and you can get the account information with the `getAccount` method.  

#### `getAccount()`

Get the authenticated account information.

    $user = $oauth->getAccount();
    
The return value is an array with following attributes:
<pre>
- id    (string) The unique identifier of the account.
- name  (string) The display name of the user.
- email (string) The email address of the user.
</pre>

#### `setAccessToken`

Set the access token. 

    $oauth->setAccessToken($accessToken);

The access token is set automatically after the authorization, see the `authorize` method.

#### `getAccessToken()`

Get the access token.

    $accessToken = $oauth->getAccessToken();

The access token is available if the user was authenticated or if you have set a token manually by the `setAccessToken` 
method.
 
#### `hasAccessToken()`

Determine if the access token is exist.
     
    $isAuthorized = $oauth->hasAccessToken();

## Contribution Guide

Are you missing a socialite driver? Then fork the repository, add a new driver in the `SocialMediaDrivers` folder and 
make a pull request. There are already a few classes in the `SocialMediaDrivers` folder, which you can orientate to the 
development. You'll see, it's not too difficult. 
