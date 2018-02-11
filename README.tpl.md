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

>>>tests/MyAwesomePackagistClientTest.php<<<

### 2. Run test to record cassette (test will be incomplete)

Cassette ``tests/vhs_cassettes/MyAwesomePackagistClientTest_testSuccessSignUp.json`` content:

>>>tests/vhs_cassettes/MyAwesomePackagistClientTest_testSuccessSignUp.json<<<

### 3. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.


## Use for server testing:

### 1. Write test with ```VhsServerTestCase``` trait. 

>>>tests/MyAwesomePackagistServerTest.php<<<

### 2. Run test to record cassette (test will be incomplete)

### 3. Prepare cassette

Remove from cassette unnecessary fields and replace dynamic parts with ```***``` sign.

Cassette ``tests/vhs_cassettes/MyAwesomePackagistServerTest_testSuccessSignUp.json`` content:
 
>>>tests/vhs_cassettes/MyAwesomePackagistServerTest_testSuccessSignUpResponse.json<<< 

### 4. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.

## Configuration
Custom cassettes directory:

In code | phpunit.xml | env vars
------- | ----------- | --------
```$this->useVhsCassettesFrom(__DIR__.'./vhs')``` | ```<env name="VHS_DIR" value="./vhs"/>``` | ```VHS_DIR=./vhs ./vendor/bin/phpunit```