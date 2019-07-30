<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class AwesomeClientTest extends TestCase
{
    use VhsTestCase;

    /** @var AwesomeClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new AwesomeClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->packagistClient = $client;
    }

    public function testSuccessStory(): void
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getFirstTagName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });
    }

    public function testWithCustomCassetteName(): void
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getFirstTagName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        }, 'testCustomName');
    }

    public function testWithFail(): void
    {
        $this->assertVhs(function () {
            $resp = $this->packagistClient->auth();
            $this->assertEquals(403, $resp);
        });
    }
}
