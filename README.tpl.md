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

>>>tests/AwesomeClientOfflineTest.php<<<

### 2. Run test to record cassette (test will be incomplete)

Cassette ``tests/vhs_cassettes/AwesomeClientOfflineTest_testSuccessStory.json`` content:

>>>tests/vhs_cassettes/AwesomeClientOfflineTest_testSuccessStory.json<<<

### 3. Run test again

If the cassette is already exists, then we will check the request and replace the response to the one recorded in the cassette.