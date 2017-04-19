<?php

namespace FashionGuide\Oauth2;

use FashionGuide\Oauth2\Exceptions\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * @method array get(string $uri, array $options = [])
 * @method array post(string $uri, array $options = [])
 * @method array put(string $uri, array $options = [])
 * @method array delete(string $uri, array $options = [])
 * 
 */
class Request
{
    /**
     * @var array
     */
    static protected $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $accept = 'application/json';

    /**
     * @var array
     */
    protected $formData = [];

    /**
     * @param string $token
     * @return Request
     */
    public function token(string $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $accept
     * @return Request
     */
    public function accept(string $accept)
    {
        $this->accept = $accept;
        return $this;
    }

    /**
     * @param array $formData
     * @return Request
     */
    public function formData(array $formData)
    {
        $this->formData = $formData;
        return $this;
    }

    /**
     * @param $method
     * @param $arguments
     * 
     * @return array|null
     */
    public function __call($method, $arguments)
    {
        if (self::hasMethod($method)) {
            $uri = $arguments[0];
            return $this->request($method, $uri);
        }
    }

    public function request($method, $uri)
    {
        try {

            $options = [
                'headers' => [
                    'Accept' => $this->accept
                ]
            ];
            
            if ($this->token) {
                $options['headers']['Authorization'] = 'Bearer ' . $this->token;
            }

            if ($this->formData) {
                $options['form_params'] = $this->formData;
            }

            $response = (new Client)->request($method, $uri, $options);
            return json_decode($response->getBody(), true);
            
        } catch (ClientException $e) {
            throw new RequestException($e);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasMethod($name)
    {
        return in_array(strtoupper($name), self::$verbs);
    }
}