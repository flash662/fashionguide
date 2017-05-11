<?php

namespace FashionGuide\Oauth2;

class TokenStorage
{
    const TOKEN_SESSION_KEY = '__fashion_guide_token';
    
    protected $accessToken;
    
    /**
     * @return AccessToken|null
     */
    public function get()
    {
        if (!$this->accessToken) {
            $this->accessToken = session(self::TOKEN_SESSION_KEY);
        }
        
        return $this->accessToken;
    }

    /**
     * @param AccessToken $accessToken
     */
    public function set(AccessToken $accessToken)
    {
        session([self::TOKEN_SESSION_KEY => $accessToken]);
    }

    /**
     * reset token session
     */
    public function reset()
    {
        session()->forget(self::TOKEN_SESSION_KEY);
    }

    /**
     * determine session exists
     * 
     * @return bool
     */
    public function exist()
    {
        return !empty($this->get());
    }
}