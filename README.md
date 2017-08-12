# HTTP request/response recording and mock library for PHP

[![Latest Version](https://img.shields.io/packagist/v/korchasa/blueprint.svg?style=flat-square)](https://packagist.org/packages/korchasa/blueprint)
[![Build Status](https://travis-ci.org/korchasa/blueprint.svg?style=flat-square)](https://travis-ci.org/korchasa/blueprint)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)

Install:
```bash
composer require --dev korchasa/php-vhs
```

Example:

```php
<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomeWikiClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomeWikiClient */
    private $wikiClient;

    public function setUp()
    {
        $client = new MyAwesomeWikiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->wikiClient = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs(function () {
            $userId = $this->wikiClient->getPageInfo();
            $this->assertGreaterThan(0, $userId);
        });
    }
}

```

Cassette ``tests/vhs_cassettes/MyAwesomeApiClientTest_testSuccessSignUp.json`` content

```json
{
    "request": {
        "uri": "https:\/\/www.mediawiki.org\/w\/api.php?action=query&format=json&curtimestamp=1&prop=info&list=&titles=API",
        "method": "GET",
        "body": {}
    },
    "response": {
        "status": 200,
        "body": {
            "curtimestamp": "***",
            "query": {
                "pages": {
                    "55332": {
                        "pageid": 55332,
                        "ns": 0,
                        "title": "API",
                        "contentmodel": "wikitext",
                        "pagelanguage": "en",
                        "pagelanguagehtmlcode": "en",
                        "pagelanguagedir": "ltr"
                    }
                }
            }
        }
    }
}
```

CLI commands (not implemented yet):
```
./vendor/bin/vhs - show cassettes list
./vendor/bin/vhs delete AcceptanceTest/testSuccessSignUp - delete cassette
./vendor/bin/vhs show AcceptanceTest - show cassette
```

Mock mode (not implemented yet):

In the "mock" mode, requests will not be sent. You will receive responses from recorded cassettes. 

Custom cassettes directory:

By phpunit.xml:
```xml
    <php>
        <env name="VHS_MOCK" value="true"/>
        <env name="VHS_DIR" value="./vhs"/>        
    </php>
```

By env vars:
```bash
VHS_MOCK=true VHS_DIR=./vhs ./vendor/bin/phpunit 
```
