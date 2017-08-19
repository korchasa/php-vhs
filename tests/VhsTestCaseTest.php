<?php namespace korchasa\Vhs\Tests;

use korchasa\matched\AssertMatchedTrait;
use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\TestCase;

class VhsTestCaseTest extends TestCase
{
    use VhsTestCase;
    use AssertMatchedTrait;

    /** @var MyAwesomePackagistClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new MyAwesomePackagistClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->packagistClient = $client;
    }

    public function testAssertVhs()
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getMyPackageName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });

        $this->assertEquals(
            $this->vhsCassettesDir.'/VhsTestCaseTest_testAssertVhs.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsOnRecord()
    {
        $cassettePath = $this->vhsCassettesDir.'/record_test.json';
        if (file_exists($cassettePath)) {
            unlink($cassettePath);
        }

        try {
            $this->assertVhs(function () {
                $packageName = $this->packagistClient->getMyPackageName();
                $this->assertEquals('korchasa/php-vhs', $packageName);
            }, 'record_test');
            $this->fail("Test must be incomplete");
        } catch (IncompleteTestError $e) {
        }

        $this->assertFileExists($cassettePath);
        $body = object_get(
            json_decode($this->currentVhsCassette->getAllRecordJson()),
            'response.body'
        );

        $this->assertNotEquals('', $body);
    }

    public function testAssertVhsWithCustomCassetteName()
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getMyPackageName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        }, 'testCustomName');

        $this->assertEquals(
            $this->vhsCassettesDir.'/testCustomName.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsFail()
    {
        $this->testServer = true;
        try {
            $this->assertVhs(function () {
                $packageName = $this->packagistClient->getMyPackageName();
                $this->assertEquals('korchasa/php-vhs', $packageName);
            });
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatched(
                'Given value of `response.headers.Date.0` not match pattern `***`',
                explode("\n", $e->getMessage())[0]
            );
        }
    }
}
