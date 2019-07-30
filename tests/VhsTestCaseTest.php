<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\Config;
use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\TestCase;

class VhsTestCaseTest extends TestCase
{
    use VhsTestCase;

    /** @var AwesomeClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new AwesomeClient();
        $client->setGuzzle(
            $this->connectVhs(
                $client->getGuzzle(),
                new Config(__DIR__.'/vhs_cassettes_VhsTestCaseTest')
            )
        );
        $this->packagistClient = $client;
    }

    public function testAssertVhs(): void
    {
        $this->assertVhs(function () {
            $this->packagistClient->getFirstTagName();
        });

        $this->assertEquals(
            __DIR__.'/vhs_cassettes_VhsTestCaseTest/VhsTestCaseTest_testAssertVhs.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testWithCustomCassetteName(): void
    {
        $this->assertVhs(function () {
            $this->packagistClient->getFirstTagName();
        }, 'testCustomName');

        $this->assertEquals(
            __DIR__.'/vhs_cassettes_VhsTestCaseTest/testCustomName.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testIncompleteOnFirstRun(): void
    {
        $cassettePath = $this->vhsConfig->resolveCassettePath(__CLASS__, 'testAssertVhsIncompleteOnFirstRun');
        if (file_exists($cassettePath)) {
            unlink($cassettePath);
        }

        try {
            $this->assertVhs(function () {
                $packageName = $this->packagistClient->getFirstTagName();
                $this->assertEquals('korchasa/php-vhs', $packageName);
            }, 'testAssertVhsIncompleteOnFirstRun');
            $this->fail('Test must be incomplete');
        } catch (IncompleteTestError $e) {
            $this->assertFileExists($cassettePath);
        }
    }
}
