<?php namespace korchasa\Vhs\Tests;

use korchasa\matched\AssertMatchedTrait;
use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class VhsTestCaseTest extends TestCase
{
    use VhsTestCase;
    use AssertMatchedTrait;

    /** @var MyAwesomeWikiClient */
    private $wikiClient;

    public function setUp()
    {
        $client = new MyAwesomeWikiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->wikiClient = $client;
    }

    public function testAssertVhs()
    {
        $this->assertVhs(function () {
            $userId = $this->wikiClient->getPageInfo();
            $this->assertGreaterThan(0, $userId);
        });

        $this->assertEquals(
            $this->vhsCassettesDir.'/VhsTestCaseTest_testAssertVhs.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsWithCustomCassetteName()
    {
        $this->assertVhs(function () {
            $userId = $this->wikiClient->getPageInfo();
            $this->assertGreaterThan(0, $userId);
        }, 'testCustomName');

        $this->assertEquals(
            $this->vhsCassettesDir.'/testCustomName.json',
            $this->currentVhsCassette->path()
        );
        $this->assertFileExists($this->currentVhsCassette->path());
    }

    public function testAssertVhsFail()
    {
        try {
            $this->assertVhs(function () {
                $userId = $this->wikiClient->getPageInfo();
                $this->assertGreaterThan(0, $userId);
            });
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatched(
                "***-'2017-08-12T22:32:26Z***'",
                $e->getMessage()
            );
        }
    }
}
