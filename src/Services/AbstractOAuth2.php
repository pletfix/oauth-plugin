<?php

namespace Pletfix\OAuth\Services;

use Pletfix\OAuth\Exceptions\OAuthException;
use Pletfix\OAuth\Services\Contracts\OAuth as OAuthContract;

/**
 * Examples for OAuth2 providers:
 *  - Amazon
 *  - Dropbox
 *  - Facebook
 *  - GitHub
 *  - Google
 *  - Instagram
 *  - LinkedIn
 *  - Microsoft
 *  - PayPal
 *  - Pinterest
 *  - Spotify
 *  - Yahoo
 */

abstract class AbstractOAuth2 implements OAuthContract
{
    /**
     * Authorize URL.
     *
     * This URL starts the OAuth 2.0 authorization flow.
     *
     * @var string
     */
    protected $authorizeUrl;

    /**
     * An app calls this endpoint to acquire a bearer token once the user has authorized the app.
     *
     * @var string
     */
    protected $tokenUrl;

    /**
     * Connection Settings.
     *
     * @var array
     */
    protected $config;

    /**
     * Access Token.
     *
     * @var string|null
     */
    protected $accessToken;

    /**
     * Create a new Auth instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function authorize()
    {
        if ($this->hasAccessToken()) {
            return true;
        }

        $input = request()->input();

        if (isset($input['error']) && $input['error'] == 'access_denied') {
            return false;
        }

        // Step 1: Redirect to the login screen on the OAuth provider.
        if (!isset($input['code'])) {
            $state = random_string(60);
            session()->set('_oauth_state', $state);
            header('Location: ' . $this->loginScreenURL($state));
            exit;
        }

        // Step 2: After then the browser is redirected back to our host and the token credentials will be saved.
        if (isset($input['code'])) {
            $state = session('_oauth_state');
            session()->delete('_oauth_state');
            if (!isset($input['state']) || $state !== $input['state']) {
                abort(HTTP_STATUS_FORBIDDEN);
            }

            $this->accessToken = $this->exchangeAuthCodeForAccessToken($state, $input['code']);

            return true;
        }

        return false;
    }

    /**
     * Get the full URL to redirect to the login screen on the OAuth provider.
     *
     * On this URL, the user can login their account and authorize your app to access their data.
     *
     * @param string $state Random string
     * @return string
     */
    abstract protected function loginScreenURL($state);

    /**
     * Exchange the auth code for a a bearer access token.
     *
     * The method is called once the user has authorized the app.
     *
     * @param string $state Random string, originally passed to the authorize URL
     * @param string $code Auth code
     * @return string Access token
     */
    abstract protected function exchangeAuthCodeForAccessToken($state, $code);

    /**
     * @inheritdoc
     */
    abstract public function getAccount();

    /**
     * @inheritdoc
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function hasAccessToken()
    {
        return !empty($this->accessToken);
    }

    /**
     * Send a HTTP request.
     *
     * @param string $url
     * @param array $post
     * @param array $headers
     * @return mixed
     * @throws OAuthException
     */
    protected function send($url, array $post = null, array $headers = [])
    {
        $headers[] = 'Accept: application/json';

        if (isset($this->config['user_agent'])) {
            $headers[] = 'User-Agent: ' . $this->config['user_agent'];
        }

        if ($this->hasAccessToken()) {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $response = curl_exec($ch);

        $result = json_decode($response);
        if (!empty($result->error)) {
            throw new OAuthException($result->error
                . (isset($result->message) ? ': ' . $result->message : '')
                . (isset($result->error_description) ? ': ' . $result->error_description : '')
            );
        }

        return $result;
    }
}