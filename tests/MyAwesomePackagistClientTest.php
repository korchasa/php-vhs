<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomePackagistClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomePackagistClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new MyAwesomePackagistClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->packagistClient = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getMyPackageName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });
    }
}
