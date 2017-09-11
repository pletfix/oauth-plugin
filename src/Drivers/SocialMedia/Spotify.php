<?php

namespace Pletfix\OAuth\Drivers\SocialMedia;

use Core\Services\Contracts\Response;
use Pletfix\OAuth\Services\AbstractOAuth2;

class Spotify extends AbstractOAuth2
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
        return 'https://accounts.spotify.com/authorize?' . http_build_query([
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_to'],
            'state'         => $state,
            'scope'         => 'user-read-email',
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
        $token = $this->send('https://accounts.spotify.com/api/token', [
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_to'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ]);

        if (!isset($token->access_token) || !isset($token->scope) || !in_array('user-read-email', explode(',', $token->scope))) {
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
        $account = $this->send('https://api.spotify.com/v1/me');

        if (!isset($account->id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return [
            'id'    => $account->id,
            'name'  => $account->display_name,
            'email' => isset($account->email) ? $account->email : null,
        ];
    }
}