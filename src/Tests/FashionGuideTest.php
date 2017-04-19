<?php

namespace FashionGuide\Oauth2\Tests;

use FashionGuide\Oauth2\FashionGuide;
use Illuminate\Config\Repository;
use Tests\TestCase;

class FashionGuideTest extends TestCase
{
    public function testLoginUrl()
    {
        $clientId = random_int(1, 100);
        $redirectUri = 'http://test.dev/callback';

        $config = new Repository([
            'fashionguide' => [
                'client_id' => $clientId,
                'client_secret' => str_random(10),
                'redirect_uri'  => $redirectUri
            ]
        ]);
        
        $fg = new FashionGuide($config);
        $loginUrl = parse_url($fg->getLoginUrl());
        parse_str($loginUrl['query'], $queryString);

        $this->assertEquals('https', $loginUrl['scheme']);
        $this->assertEquals('guard.fashionguide.com.tw', $loginUrl['host']);
        $this->assertEquals('/oauth/authorize', $loginUrl['path']);

        $this->assertEquals($clientId, $queryString['client_id']);
        $this->assertEquals($redirectUri, $queryString['redirect_uri']);
        $this->assertEquals('code', $queryString['response_type']);
    }
}
