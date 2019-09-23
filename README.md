# HTTP request/response recording and mock library for PHP

![Usage](http://i.imgur.com/XqnAxyp.gif)

[![Latest Version](https://img.shields.io/packagist/v/korchasa/php-vhs.svg?style=flat-square)](https://packagist.org/packages/korchasa/php-vhs)
[![Build Status](https://travis-ci.org/korchasa/php-vhs.svg?style=flat-square)](https://travis-ci.org/korchasa/php-vhs)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)

## Install:
```bash
composer require --dev korchasa/php-vhs
```

## Client testing:
 
### 1. Write test with ```VhsTestCase``` trait. Surround client calls with ```assertVhs()```.

```php
<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class AwesomeClientOfflineTest extends TestCase
{
    use VhsTestCase;

    /** @var AwesomeClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new AwesomeClient("bla-bla-bla-bla-bla-bla.commmm");
        $client->setGuzzle($this->connectVhs($client->getGuzzle(), $offline = true));
        $this->packagistClient = $client;
    }

    public function testSuccessStory(): void
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getFirstTagName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });
    }

    public function testWithFail(): void
    {
        $this->assertVhs(function () {
            $resp = $this->packagistClient->auth();
            $this->assertEquals(403, $resp);
        });
    }
}

```

### 2. Run test to record cassette (test will be incomplete)

Cassette ``tests/vhs_cassettes/AwesomeClientOfflineTest_testSuccessStory.json`` content:

```json
[
    {
        "request": {
            "uri": "https:\/\/httpbin.org\/anything?name=korchasa\/php-vhs",
            "method": "GET",
            "headers": {
                "X-Foo": [
                    "Bar"
                ],
                "Host": [
                    "httpbin.org"
                ]
            },
            "body": "",
            "body_format": "raw"
        },
        "response": {
            "status": 200,
            "headers": {
                "Content-Type": [
                    "application\/json"
                ]
            },
            "body": {
                "args": {
                    "name": "korchasa\/php-vhs"
                },
                "data": "",
                "files": {},
                "form": {},
                "headers": {
                    "Host": "httpbin.org",
                    "User-Agent": "***",
                    "X-Foo": "Bar"
                },
                "json": null,
                "method": "GET",
                "origin": "***",
                "url": "https:\/\/httpbin.org\/anything?name=korchasa%2Fphp-vhs"
            },
            "body_format": "json"
        }
    }
]
```

### 3. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.