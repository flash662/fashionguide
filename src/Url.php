<?php

namespace FashionGuide\Oauth2;

class Url
{
    const DEFAULT_BASE_URL = 'https://guard.fashionguide.com.tw';
    
    const SEPARATOR = '/';

    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct($baseUrl = null)
    {
        if ($baseUrl) {
            $this->setBaseUrl($baseUrl);
        }
    }

    /**
     * @param string $uri
     * @param array  $params
     * @return string
     */
    public function build($uri, $params = [])
    {
        $url = $this->getBaseUrl() . self::SEPARATOR . ltrim($uri, self::SEPARATOR);
        return empty($params) ? $url : $url . '?' . http_build_query($params);
    }
    
    /**
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        if (!$this->baseUrl) {
            $this->baseUrl = self::DEFAULT_BASE_URL;
        }
        
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }
}