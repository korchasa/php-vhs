<?php namespace korchasa\Vhs\Tests;

use korchasa\matched\AssertMatchedTrait;
use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class VhsTestCaseTest extends TestCase
{
    use VhsTestCase;
    use AssertMatchedTrait;

    /** @var MyAwesomeApiClient */
    private $client;

    public function setUp()
    {
        $client = new MyAwesomeApiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->client = $client;
    }

    public function testAssertVhs()
    {
        $this->assertVhs(function () {
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
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
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
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
        $this->assertVhs(function () {
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
            $this->assertGreaterThan(0, $userId);
        });

        try {
            $this->assertVhs(function () {
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
