<?php

namespace Pletfix\OAuth\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use Core\Services\Contracts\Response;
use Core\Services\DI;

/**
 * This controller handles authentication users through the OAuth provider and redirecting them to your home screen.
 */
class OAuthController extends Controller
{
    /**
     * Where to redirect users after login or logout.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Authenticate the user through the OAuth provider.
     *
     * @param string $provider Name of the provider.
     * @return Response
     */
    public function login($provider)
    {
        //$oauth = oauth($provider);
        /** @var \Pletfix\OAuth\Services\Contracts\OAuth $oauth */
        $oauth = DI::getInstance()->get('oauth-factory')->provider($provider);
        if (!$oauth->authorize()) {
            return redirect('')->withError(t('oauth.login.failed'));
        }

        // Get the account information.
        $account = array_merge(['name' => null, 'email' => null], $oauth->getAccount());
        $principal = $account['id'] . '@' . $provider;

        // Load the User entity from the database or create a new Model if not exist.
        $user = User::where('principal', $principal)->first();
        if ($user === null && !empty($account['email'])) {
            $user = User::where('email', $account['email'])->first();
        }
        if ($user === null) {
            $user = new User;
            $user->principal = $principal;
            $user->name      = $account['name'];
            $user->email     = $account['email'];
            $user->role      = config('oauth.role');
            $user->save();
        }

        // Log the user into the application.
        auth()->setPrincipal($user->id, $user->name, $user->role);

        $url = session('origin_url', url($this->redirectTo));

        return response()->redirect($url);
    }

    /**
     * Log off the user from the application.
     *
     * @return Response
     */
    public function logout()
    {
        auth()->logout();

        return redirect($this->redirectTo);
    }
}
