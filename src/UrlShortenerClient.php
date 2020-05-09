<?php

namespace milypoint\urlshortenerclient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class UrlShortenerClient
{
    private $_attributes = [];

    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->_attributes[$name];
    }

    public function __construct($params=[])
    {
        $this->base_uri = 'http://urlshortener.local';
        $this->timeout = 10.0;

        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->_attributes)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param string $url
     * @return string
     * @throws NotValidDataException
     * @throws UrlClientException
     */
    public function request($url)
    {
        $client = new Client([
            'base_uri' => $this->base_uri,
            'timeout'  => $this->timeout,
        ]);
        try {
            $response = $client->post('/', [
                'json' => [
                    'url' => $url,
                ]
            ]);
            $body = json_decode($response->getBody());
            return $body->code;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $message = $e->getResponse()->getBody()->getContents();
            if ($response->getStatusCode() == 422) {
                throw new NotValidDataException($message, $response->getStatusCode(), $e);
            }
            throw new UrlClientException($message, $response->getStatusCode(), $e);
        }
    }
}