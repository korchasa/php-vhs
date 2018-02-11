<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class MyAwesomePackagistClient
{
    protected $guzzle;

    public function __construct(string $host = 'packagist.org')
    {
        $this->guzzle = new Client([
            'base_uri' => "https://$host/",
            'http_errors' => false,
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

    public function getMyPackageName(): string
    {
        $response = $this->guzzle->get('p/korchasa/php-vhs.json');
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException("500 response from packagist.org");
        }
        $responseJson = $response->getBody()->getContents();
        $responseData = json_decode($responseJson, true);
        return $responseData['packages']['korchasa/php-vhs']['0.1-alpha']['name'];
    }
}
