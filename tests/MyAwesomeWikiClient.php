<?php namespace korchasa\Vhs\Tests;

use GuzzleHttp\Client;

class MyAwesomeWikiClient
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client(['base_uri' => 'https://www.mediawiki.org/w/api.php']);
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

    public function getPageInfo(): int
    {
        $this->guzzle->get('?action=query&format=json&curtimestamp=1&prop=info&list=&titles=API');
        return 1;
    }
}
