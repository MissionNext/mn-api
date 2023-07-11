<?php

namespace App\Modules\Api\Auth;

use Illuminate\Support\Facades\App;
use App\Modules\Api\Exceptions\AuthenticationException;
use App\Models\Application\Application;
use App\Modules\Api\Facade\SecurityContext as FSecContext;
use App\Modules\Api\Auth\SecurityContext as AuthSecurityContext;

class Manager
{
    /** @var  Token */
    private $token;

    /**
     * @param Token $token
     *
     * @return Token
     *
     * @throws AuthenticationException
     */
    public function authenticate(Token $token)
    {
     //   var_dump($token);
        $current_timestamp = time();
        $this->token = $token;
        $application = Application::where('public_key', $token->publicKey)->first();

        if (!$application) {
            throw new AuthenticationException("Authentication failed", 5);
        }

        $token->setApp($application);

        App::instance('rest.token', $token);

        App::setLocale($token->language()->key ?? 'uk');

        // AuthSecurityContext::setToken($token);

       FSecContext::setToken($token);

        if (!App::environment('local', 'stage')) {
            if (($current_timestamp - $token->created) > 120) { //@TODO fix timestamp authentication
                throw new AuthenticationException("Timed out", 1);
            } elseif (($current_timestamp < $token->created)) {
                throw new AuthenticationException("Invalid timestamp", 2);
            }
        }


        if (!$this->validateHash($application)) {
            throw new AuthenticationException("Private Key Exception", 4);
        }

        return $token;
    }

    /**
     * @param $application
     *
     * @return bool
     */
    protected function validateHash($application)
    {
        $hash = strtr(base64_encode(
            hash_hmac('sha1', $this->token->uri,
                base64_decode(strtr($application->private_key, '-_', '+/')), true)), '+/', '-_');
        //return true;
        return ($hash === $this->token->hash) || App::environment('local');
    }

}
