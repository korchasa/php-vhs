<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomeApiClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomeApiClient */
    private $client;

    public function setUp()
    {
        $client = new MyAwesomeApiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->client = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs(function () {
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
            $this->assertGreaterThan(0, $userId);
        });
    }
}
