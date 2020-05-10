<?php

namespace milypoint\urlshortenerclient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class UrlShortenerClient
{
    /**
     * @var string
     */
    public $base_uri = 'http://urlshortener.local';

    /**
     * @var float
     */
    public $timeout = 10.0;

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