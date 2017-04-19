<?php

namespace FashionGuide\Oauth2;

use Carbon\Carbon;
use FashionGuide\Oauth2\Exceptions\AppException;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class AccessToken
 * @package FashionGuide\Oauth2
 */
class AccessToken implements Jsonable
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @var integer
     */
    protected $expireIn;

    /**
     * @return int
     */
    public function getExpireIn()
    {
        return $this->expireIn;
    }

    /**
     * @var Carbon
     */
    protected $expireAt;

    /**
     * @return Carbon
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * 
     * @param $options
     * @throws AppException
     */
    public function __construct($options)
    {
        if (!isset($options['access_token']) || !isset($options['expires_in'])) {
            throw new AppException('missing params: access_token or expires_in');
        }

        $this->token        = $options['access_token'];
        $this->expireIn     = $options['expires_in'];
        $this->refreshToken = $options['refresh_token'];

        $this->expireAt = $this->calcExpiredAt();
    }

    /**
     * @return Carbon
     */
    protected function calcExpiredAt()
    {
        return Carbon::now()->addSeconds($this->expireIn);
    }

    /**
     *
     * @return boolean
     */
    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expireAt);
    }

    public function __toString()
    {
        return $this->getToken();
    }

    public function toJson($options = 0)
    {
        return json_encode([
            'expireIn' => $this->expireIn,
            'expireAt' => $this->expireAt->toDateTimeString(),
            'token'    => $this->token
        ]);
    }

    public function hasRefreshToken()
    {
        return !empty($this->refreshToken);
    }
}