<?php

namespace Pletfix\OAuth\Drivers\SocialMedia;

use Core\Services\Contracts\Response;
use Pletfix\OAuth\Services\AbstractOAuth2;

class Dropbox extends AbstractOAuth2
{
    /**
     * Get the full URL to redirect to the login screen on the OAuth provider.
     *
     * On this URL, the user can login their account and authorize your app to access their data.
     *
     * @param string $state Random string
     * @return string
     */
    protected function loginScreenURL($state)
    {
        return 'https://www.dropbox.com/oauth2/authorize?' . http_build_query([
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_to'],
            'state'         => $state,
            'response_type' => 'code',
        ]);
    }

    /**
     * Exchange the auth code for a a bearer access token.
     *
     * The method is called once the user has authorized the app.
     *
     * @param string $state Random string, originally passed to the authorize URL
     * @param string $code Auth code
     * @return string Access token
     */
    protected function exchangeAuthCodeForAccessToken($state, $code)
    {
        $token = $this->send('https://api.dropboxapi.com/oauth2/token', [
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_to'],
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ]);

        if (!isset($token->access_token)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $token->access_token;
    }

    /**
     * @inheritdoc
     */
    public function getAccount()
    {
        # fetch user information
        $account = $this->send('https://api.dropboxapi.com/1/account/info');

        if (!isset($account->uid)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return [
            'id'    => $account->uid,
            'name'  => $account->display_name,
            'email' => isset($account->email) ? $account->email : null,
        ];
    }
}