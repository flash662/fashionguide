<?php
namespace FashionGuide\Oauth2;

use FashionGuide\Oauth2\Exceptions\AppException;
use FashionGuide\Oauth2\Exceptions\RequestException;
use FashionGuide\Oauth2\TokenGenerators\ { 
    AbstractGenerator, 
    AuthorizationCode, 
    RefreshToken 
};
use Illuminate\Config\Repository;

/**
 *
 * @method array get(string $uri, array $options = []) 
 * @method array post(string $uri, array $options = []) 
 * @method array put(string $uri, array $options = []) 
 * @method array delete(string $uri, array $options = []) 
 * @package FashionGuide\Oauth2
 */
class FashionGuide
{
    /**
     * @var Repository
     */
    protected $config;
    
    /**
     * @var AccessToken
     */
    protected $accessToken = null;

    /**
     * @var AbstractGenerator
     */
    protected $tokenGenerator;

    /**
     * @var Url
     */
    protected $url;

    /**
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param Url $url
     * @return $this
     */
    public function setUrl(Url $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param $uri
     * @param array $params
     * @return Url|string
     */
    public function getUrl($uri = null, $params = [])
    {
        if (!$this->url) {
            $this->url = new Url($this->config->get('fashionguide.base_url'));
        }
        
        return $uri ? $this->url->build($uri, $params) : $this->url;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->config->get('fashionguide.client_id');
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->config->get('fashionguide.client_secret');
    }

    /**
     * @param AbstractGenerator|null $tokenGenerator
     * 
     * @return AccessToken
     * @throws RequestException
     */
    public function generateToken($tokenGenerator = null)
    {
        if (!$tokenGenerator instanceof AbstractGenerator) {
            $tokenGenerator = $this->defaultAccessTokenGenerator();
        }
        
        $accessToken = $tokenGenerator->getAccessToken();
        return $this->accessToken = tap($accessToken, function ($accessToken) {
            $this->getTokenStorage()->set($accessToken);
        });
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->config->get('fashionguide.redirect_uri');
    }

    /**
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getUrl('/oauth/authorize', [
            'client_id'     => $this->getClientId(),
            'redirect_uri'  => $this->getRedirectUrl(),
            'response_type' => 'code',
            'scope'         => ''
        ]);
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        if (!$this->accessToken instanceof AccessToken) {
            $this->accessToken = $this->loadOrGenerateToken();
        }
        
        return $this->accessToken;
    }

    /**
     * 
     * @return AccessToken|null
     */
    private function loadAccessTokenFromSession()
    {
        $storage = $this->getTokenStorage();
        
        if (!$storage->exist()) {
            return null;
        }
        
        if ($storage->get()->isExpired()) {
            $storage->reset();
            return null;
        }
        
        return $storage->get();
    }
    
    /**
     * @return AbstractGenerator
     */
    protected function defaultAccessTokenGenerator()
    {
        return new AuthorizationCode($this);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array
     * 
     * @throws AppException
     * @throws RequestException
     */
    public function request($method, $uri, $params = [])
    {
        $accessToken = $this->getAccessToken();

        if ($accessToken->isExpired()) {

            if (!$accessToken->hasRefreshToken()) {
                throw new AppException('Access token is expired');
            }

            $accessToken = $this->refreshToken();
        }
        
        return (new Request)->token($accessToken)
            ->formData($params)
            ->$method($this->getUrl($uri));
    }

    /**
     * @return AccessToken
     */
    protected function refreshToken()
    {
        return $this->generateToken(new RefreshToken($this));
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return array
     * @throws AppException
     */
    public function __call($method, $arguments)
    {
        if (Request::hasMethod($method)) {
            
            if (func_num_args() > 1) {
                $uri    = $arguments[0];
                $params = isset($arguments[1]) ? $arguments[1] : [];
                return $this->request($method, $uri, $params);
            }
        }
        
        throw new AppException('undefined method');
    }

    public function logout()
    {
        $this->accessToken = null;
        $this->getTokenStorage()->reset();
    }

    /**
     * @return AccessToken|null
     */
    private function loadOrGenerateToken()
    {
        return $this->loadAccessTokenFromSession() ?: $this->generateToken();
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return app('FG.tokenStorage');
    }
}