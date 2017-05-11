<?php

namespace FashionGuide\Oauth2\TokenGenerators;

use FashionGuide\Oauth2\AccessToken;
use FashionGuide\Oauth2\FashionGuide;

abstract class AbstractGenerator
{
    /**
     * @var FashionGuide
     */
    protected $fashionGuide;
    
    public function __construct(FashionGuide $fashionGuide)
    {
        $this->fashionGuide = $fashionGuide;
    }

    /**
     * @return \FashionGuide\Oauth2\Url
     */
    protected function getUrl()
    {
        return $this->fashionGuide->getUrl();
    }
    
    /**
     * @return AccessToken
     * @throws \Exception
     */
    abstract public function getAccessToken();
}