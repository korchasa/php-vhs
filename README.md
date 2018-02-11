# HTTP request/response recording and mock library for PHP

![Usage](http://i.imgur.com/XqnAxyp.gif)

[![Latest Version](https://img.shields.io/packagist/v/korchasa/php-vhs.svg?style=flat-square)](https://packagist.org/packages/korchasa/php-vhs)
[![Build Status](https://travis-ci.org/korchasa/php-vhs.svg?style=flat-square)](https://travis-ci.org/korchasa/php-vhs)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)

## Install:
```bash
composer require --dev korchasa/php-vhs
```

## Usage for client testing:
 
### 1. Write test with ```VhsTestCase``` trait. Surround client calls with ```assertVhs()```.

```php
<?php namespace korchasa\Vhs\Tests;

use korchasa\Vhs\VhsTestCase;
use PHPUnit\Framework\TestCase;

class MyAwesomePackagistClientTest extends TestCase
{
    use VhsTestCase;

    /** @var MyAwesomePackagistClient */
    private $packagistClient;

    public function setUp()
    {
        $client = new MyAwesomePackagistClient('some-not-existent-site.foobar');
        $client->setGuzzle($this->connectVhs($client->getGuzzle()));
        $this->packagistClient = $client;
    }

    public function testSuccessSignUp()
    {
        $this->assertVhs(function () {
            $packageName = $this->packagistClient->getMyPackageName();
            $this->assertEquals('korchasa/php-vhs', $packageName);
        });
    }
}

```

### 2. Run test to record cassette (test will be incomplete)

Cassette ``tests/vhs_cassettes/MyAwesomePackagistClientTest_testSuccessSignUp.json`` content:

```json
{
    "request": {
        "uri": "https:\/\/packagist.org\/p\/korchasa\/php-vhs.json",
        "method": "GET",
        "body_format": "raw",
        "headers": {
            "User-Agent": [
                "GuzzleHttp\/6.2.1 curl\/7.54.0 PHP\/7.2.0beta2"
            ],
            "Host": [
                "packagist.org"
            ]
        },
        "body": ""
    },
    "response": {
        "status": 200,
        "headers": {
            "Server": [
                "nginx"
            ],
            "Date": [
                "Sat, 19 Aug 2017 15:01:30 GMT"
            ]
        },
        "body_format": "json",
        "body": {
            "packages": {
                "korchasa\/php-vhs": {
                    "dev-master": {
                        "name": "korchasa\/php-vhs",
                        "description": "HTTP request\/response recording and mock library for PHP",
                        "keywords": [
                            "http",
                            "testing",
                            "phpunit",
                            "Guzzle",
                            "vcr"
                        ],
                        "homepage": "",
                        "version": "dev-master",
                        "version_normalized": "9999999-dev",
                        "license": [
                            "MIT"
                        ],
                        "authors": [
                            {
                                "name": "korchasa",
...

```

### 3. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.


## Usage for server testing:

### 1. Enable server testing mode with ```$this->testServer = true;```

```php
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

```

### 2. Run test to record cassette (test will be incomplete)

### 3. Prepare cassette

Remove from cassette unnecessary fields and replace dynamic parts with ```***``` sign.

Cassette ``tests/vhs_cassettes/MyAwesomePackagistServerTest_testSuccessSignUp.json`` content:
 
```json
{
    "request": {
        "uri": "https:\/\/packagist.org\/p\/korchasa\/php-vhs.json",
        "method": "GET",
        "body_format": "raw",
        "headers": {
            "User-Agent": [
                "GuzzleHttp\/6.2.1 curl\/7.54.0 PHP\/7.2.0beta2"
            ],
            "Host": [
                "packagist.org"
            ]
        },
        "body": ""
    },
    "response": {
        "status": 200,
        "headers": {
            "Server": [
                "nginx"
            ],
            "Date": [
                "Sat, 19 Aug 2017 15:01:30 GMT"
            ]
        },
        "body_format": "json",
        "body": {
            "packages": {
                "korchasa\/php-vhs": {
                    "dev-master": {
                        "name": "korchasa\/php-vhs",
                        "description": "HTTP request\/response recording and mock library for PHP",
                        "keywords": [
                            "http",
                            "testing",
                            "phpunit",
                            "Guzzle",
                            "vcr"
                        ],
                        "homepage": "",
                        "version": "dev-master",
                        "version_normalized": "9999999-dev",
                        "license": [
                            "MIT"
                        ],
                        "authors": [
                            {
                                "name": "korchasa",
...

``` 

### 4. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.

## Configuration
Custom cassettes directory:

In code | phpunit.xml | env vars
------- | ----------- | --------
```$this->useVhsCassettesFrom(__DIR__.'./vhs')``` | ```<env name="VHS_DIR" value="./vhs"/>``` | ```VHS_DIR=./vhs ./vendor/bin/phpunit```