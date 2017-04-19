<?php
namespace FashionGuide\Oauth2\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;

/**
 * 
 * @method string getError()
 * @method string getHint()
 */
class RequestException extends \Exception
{
    /**
     * @var array
     */
    protected $error = [];

    /**
     * 
     * @param ClientException|null $previous
     */
    public function __construct(ClientException $previous = null)
    {
        $this->error = $this->decodeMessage($previous);
        $message = isset($this->error['message']) ? $this->error['message'] : '';
        
        parent::__construct($message, $previous->getCode(), $previous);
    }

    /**
     * @param ClientException $exception
     * @return array
     */
    protected function decodeMessage(ClientException $exception)
    {
        if ($exception->getCode() == 404) {
            return ['message' => 'not found'];
        }
        
        return (array)json_decode($exception->getResponse()->getBody(), true);
    }

    public function __call($name, $args)
    {
        if (Str::startsWith($name, 'get')) {
            $name = Str::camel(Str::substr($name, 3));
            return isset($this->error[$name]) ? $this->error[$name] : null;
        }
    }

    public function __invoke($name)
    {
        return isset($this->error[$name]) ? $this->error[$name] : null;
    }
}