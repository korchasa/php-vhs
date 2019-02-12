<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class AwesomeClient
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client([
            'base_uri' => 'https://api.travis-ci.org/',
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

    public function auth()
    {
        $response = $this->guzzle->post('/auth/github');
        $responseJson = $response->getBody()->getContents();
        return json_decode($responseJson, true);
    }

    public function getFirstTagName(): string
    {
        $response = $this->guzzle->get('/repos/korchasa/php-vhs');
        $responseJson = $response->getBody()->getContents();
        $responseData = json_decode($responseJson, true);
        return $responseData['slug'];
    }
}