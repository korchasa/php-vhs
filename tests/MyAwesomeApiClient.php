<?php namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class MyAwesomeApiClient
{
    public function setGuzzle(Client $client)
    {
    }

    public function getGuzzle(): Client
    {}

    public function signUp(string $login, string $password): integer
    {
        return 1;
    }
}