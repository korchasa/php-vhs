<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsServerTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomePackagistServerTest extends TestCase
{
    use VhsServerTestCase;

    public function testSuccessSignUp()
    {
        $this->assertValidServerResponse(__DIR__.'/vhs_cassettes/MyAwesomePackagistServerTest_*');
    }
}
