# HTTP request/response recording and mock library for PHP

<!-- [![Latest Version](https://img.shields.io/packagist/v/korchasa/blueprint.svg?style=flat-square)](https://packagist.org/packages/korchasa/blueprint)
[![Build Status](https://travis-ci.org/korchasa/blueprint.svg?style=flat-square)](https://travis-ci.org/korchasa/blueprint)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/) -->

Install:
```bash
composer require --dev korchasa/php-vhs
```

Example:
```php
<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomeApiClientTest extends TestCase
{
    use VhsTestCase;

    /**
     * @var MyAwesomeApiClient
     */
    private $client;

    public function setUp()
    {
        $this->useVhsCassettesFrom('vhs_cassettes');
        $client = new MyAwesomeApiClient();
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->client = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs('signUp', function() {
            $userId = $this->client->signUp('Cheburashka', 'Passw0rd');
            $this->assertGreaterThan(0, $userId);
        });
    }
}
```

Cassette ``./tests/vhs_cassettes/my_awesome_api_client_test.json`` content
```json
[
  {
    "request": {
      "url": "http://myawesomeapi.com"
    },
    "response": {
      "status_code": 200,
      "headers": {
        "Content-Length": "***",
        "Content-Type": "text/json"        
      },
      "body": {
        "id": 1
      }
    }
  }
]
```


CLI commands:
```
./vendor/bin/vhs - show cassettes list
./vendor/bin/vhs delete AcceptanceTest/testSuccessSignUp - delete cassette
./vendor/bin/vhs show AcceptanceTest - show cassette
```

Configuration:

By phpunit.xml:
```xml
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/6.3/phpunit.xsd">
    <!-- ... -->
    <php>
        <env name="VHS_MOCK" value="true"/>
    </php>
</phpunit>
```

By env vars:
```bash
vhs.mock=true ./vendor/bin/phpunit 
```
