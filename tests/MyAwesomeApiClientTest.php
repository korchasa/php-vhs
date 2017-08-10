<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class MyAwesomeApiClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomeApiClient */
    private $client;

    public function setUp()
    {
        $client = new MyAwesomeApiClient();
        $client->setGuzzle($this->connectVhs(__DIR__.'/vhs_cassettes', $client->getGuzzle()));
        $this->client = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs('signUp', function () {
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
            $this->assertGreaterThan(0, $userId);
        });
    }

    public function testFailSignUp()
    {
        try {
            $this->assertVhs('signUp', function () {
                $userId = $this->client->signUp('Cheburashka', 'WRONG PASSWORD');
                $this->assertGreaterThan(0, $userId);
            });
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertNotFalse(strpos(
                $e->getMessage(),
                "-'Passw0rd'\n+'WRONG PASSWORD'"
            ));
        }
    }
}
