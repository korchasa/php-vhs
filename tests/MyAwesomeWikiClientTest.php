<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomeWikiClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomeWikiClient */
    private $wikiClient;

    public function setUp()
    {
        $client = new MyAwesomeWikiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->wikiClient = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs(function () {
            $userId = $this->wikiClient->getPageInfo();
            $this->assertGreaterThan(0, $userId);
        });
    }
}
