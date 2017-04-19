<?php

namespace FashionGuide\Oauth2\TokenGenerators\Traits;

use FashionGuide\Oauth2\AccessToken;
use FashionGuide\Oauth2\Exceptions\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

trait PassportRequest
{
    /**
     * @param $url
     * @param \Closure $closure
     * @return AccessToken
     * @throws RequestException
     */
    protected function request($url, \Closure $closure)
    {
        try {
            
            $result = json_decode($this->sendPost($url, $closure)->getBody(), true);
            return new AccessToken($result);

        } catch (ClientException $e) {
            throw new RequestException($e);
        }
    }

    /**
     * @param string   $url
     * @param \Closure $config
     * @return ResponseInterface
     */
    protected function sendPost($url, \Closure $config)
    {
        return (new Client)->post($url, ['form_params' => $config()]);
    }
}