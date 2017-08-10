<?php namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class MyAwesomeApiClient
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client(['base_uri' => 'http://httpbin.org/']);
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

    public function signUp(string $login, string $password): int
    {
        $this->guzzle->post('/post', ['body' => json_encode(func_get_args())]);
        return 1;
    }
}
