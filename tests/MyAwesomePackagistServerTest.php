<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsServerTestCase;
use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomePackagistServerTest extends TestCase
{
    use VhsTestCase;
    use VhsServerTestCase;

    /** @var MyAwesomePackagistClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new MyAwesomePackagistClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->packagistClient = $client;
    }

    public function testSuccessSignUpResponse()
    {
        $this->assertValidServerResponse(
            $this->assertVhs(function () {
                $this->assertEquals(
                    'korchasa/php-vhs',
                    $this->packagistClient->getMyPackageName()
                );
            })
        );
    }

    public function testAllCassettesByPattern()
    {
        $this->assertValidServerResponse(__DIR__.'/vhs_cassettes/MyAwesomePackagistServerTest_*');
    }
}
