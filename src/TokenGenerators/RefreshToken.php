<?php

namespace FashionGuide\Oauth2\TokenGenerators;

use FashionGuide\Oauth2\AccessToken;
use FashionGuide\Oauth2\Exceptions\AppException;
use FashionGuide\Oauth2\TokenGenerators\Traits\PassportRequest;

class RefreshToken extends AbstractGenerator
{
    use PassportRequest;
    
    /**
     * @return AccessToken
     * @throws AppException
     */
    public function getAccessToken()
    {
        return $this->request($this->fashionGuide->getUrl('/oauth/token'), function () {
            return [
                'client_id'     => $this->fashionGuide->getClientId(),
                'client_secret' => $this->fashionGuide->getClientSecret(),
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->getRefreshToken()
            ];
        });
    }

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        if (!$this->refreshToken) {
            $this->refreshToken = $this->fashionGuide->getAccessToken()->getRefreshToken();
        }
        
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
}