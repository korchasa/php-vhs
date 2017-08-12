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
            $this->cassettesDir.'/VhsTestCaseTest_testAssertVhs.json',
            $this->currentCassette->path()
        );
        $this->assertFileExists($this->currentCassette->path());
    }

    public function testAssertVhsWithCustomCassetteName()
    {
        $this->assertVhs(function () {
            $userId = $this->wikiClient->getPageInfo();
            $this->assertGreaterThan(0, $userId);
        }, 'testCustomName');

        $this->assertEquals(
            $this->cassettesDir.'/testCustomName.json',
            $this->currentCassette->path()
        );
        $this->assertFileExists($this->currentCassette->path());
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
                "***-'2017-08-12T15:33:52Z'***",
                $e->getMessage()
            );
        }
    }
}
