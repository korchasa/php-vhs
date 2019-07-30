<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class AwesomeClient
{
    protected $guzzle;

    public function __construct($host)
    {
        $this->guzzle = new Client([
            'base_uri' => "https://$host/",
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'X-Foo' => 'Bar',
            ],
        ]);
    }

    public function setGuzzle(Client $client)
    {
        $this->guzzle = $client;
        return $this;
    }

    public function getGuzzle(): Client
    {
        return $this->guzzle;
    }

    public function auth(): int
    {
        $resp = $this->guzzle->post('/status/403');
        return $resp->getStatusCode();
    }

    public function getFirstTagName(): string
    {
        $response = $this->guzzle->get('/anything?name=korchasa/php-vhs');
        $responseJson = $response->getBody()->getContents();
        $responseData = json_decode($responseJson, true);
        return $responseData['args']['name'];
    }
}
