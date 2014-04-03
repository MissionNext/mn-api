<?php

namespace MissionNext\Api\Auth;

use Illuminate\Support\Facades\App;
use MissionNext\Api\Exceptions\AuthenticationException;
use MissionNext\Models\Application\Application;


class Manager
{
    /** @var  Token */
    private $token;

    /**
     * @param Token $token
     *
     * @return Token
     *
     * @throws \MissionNext\Api\Exceptions\AuthenticationException
     */
    public function authenticate(Token $token)
    {
        $current_timestamp = time();
        $this->token = $token;
        //@TODO get app from app key and set to token
        $application = Application::wherePublicKey($token->publicKey)->first();

        if (!$application){
            throw new AuthenticationException("Authentication failed", 5);
        }

        $token->setApp($application);
        App::instance('rest.token', $token);
        App::bind('security_context', function($app) use ($token){

            return new SecurityContext($token);
        });
        if (($current_timestamp - $token->created) > 60 ){//@TODO fix timestamp authentication
            throw new AuthenticationException("Timedout", 1);
        } elseif ( ($current_timestamp < $token->created) && !App::environment('local') ) {
            throw new AuthenticationException("Invalid timestamp", 2);
        }

        if (!$this->validateHash($application)){
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

        return ($hash === $this->token->hash) || App::environment('local');
    }

} 