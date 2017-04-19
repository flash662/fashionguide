<?php

namespace FashionGuide\Oauth2\TokenGenerators;

use App\Contracts\RequestResponse;
use FashionGuide\Oauth2\AccessToken;
use FashionGuide\Oauth2\Exceptions\AppException;
use FashionGuide\Oauth2\TokenGenerators\Traits\PassportRequest;
use GuzzleHttp\Client;

/**
 * Class AuthorizationCode
 * @package FashionGuide\Oauth2\TokenGenerators
 */
class AuthorizationCode extends AbstractGenerator
{
    use PassportRequest;
    
    const PARAM_NAME = 'code';

    /**
     * @return AccessToken
     * @throws AppException
     */
    public function getAccessToken()
    {
        return $this->request($this->getUrl()->build('/oauth/token'), function () {
            return [
                'client_id'     => $this->fashionGuide->getClientId(),
                'client_secret' => $this->fashionGuide->getClientSecret(),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->fashionGuide->getRedirectUrl(),
                'code'          => request(self::PARAM_NAME)
            ];
        });
    }
}