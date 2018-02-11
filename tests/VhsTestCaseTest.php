<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\matched\AssertMatchedTrait;
use korchasa\Vhs\VhsTestCase;
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
        $client = new MyAwesomePackagistClient('packagist.org');
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
            __DIR__.'/vhs_cassettes/VhsTestCaseTest_testAssertVhs.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsOnRecord()
    {
        $cassettePath = $this->vhsConfig->resolveCassettesDir(__CLASS__).'/record_test.json';
        if (file_exists($cassettePath)) {
            unlink($cassettePath);
        }

        try {
            $this->assertVhs(function () {
                $packageName = $this->packagistClient->getMyPackageName();
                $this->assertEquals('korchasa/php-vhs', $packageName);
            }, 'record_test');
            $this->fail('Test must be incomplete');
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
            __DIR__.'/vhs_cassettes/testCustomName.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsFail()
    {
        try {
            $this->assertVhs(function () {
                $packageName = $this->packagistClient->getMyPackageName();
                $this->assertEquals('korchasa/php-vhs', $packageName);
            });
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('500 response from packagist.org', $e->getMessage());
        }
    }

    public function testSuccessSignUpWithNotExistedHostAndRecordedCassette()
    {
        $client = new MyAwesomePackagistClient('some-not-existent-site.foobar');
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));

        $this->assertVhs(function () use ($client) {
            $packageName = $client->getMyPackageName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });
    }
}
