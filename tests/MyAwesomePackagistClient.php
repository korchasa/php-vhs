<?php namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class MyAwesomePackagistClient
{
    protected $guzzle;

    public function __construct($host)
    {
        $this->guzzle = new Client(['base_uri' => "https://$host/"]);
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
        $responseJson = $this->guzzle->get('p/korchasa/php-vhs.json')->getBody()->getContents();
        $response = json_decode($responseJson, true);
        return $response['packages']['korchasa/php-vhs']['dev-master']['name'];
    }
}
