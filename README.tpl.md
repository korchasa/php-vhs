# HTTP request/response recording and mock library for PHP

1. Add ```VhsTestCase``` to test
1. Surround client call with ```assertValidVhs()```
1. Run test to record cassette (test will be incomplete)
1. Replace dynamic values in cassette with ```***``` symbol, and remove unnecessary values 
1. Run test  

![Usage](http://i.imgur.com/XqnAxyp.gif)

[![Latest Version](https://img.shields.io/packagist/v/korchasa/php-vhs.svg?style=flat-square)](https://packagist.org/packages/korchasa/php-vhs)
[![Build Status](https://travis-ci.org/korchasa/php-vhs.svg?style=flat-square)](https://travis-ci.org/korchasa/php-vhs)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)

Install:
```bash
composer require --dev korchasa/php-vhs
```

Example:

>>>tests/MyAwesomeWikiClientTest.php<<<

Cassette ``tests/vhs_cassettes/MyAwesomeApiClientTest_testSuccessSignUp.json`` content

>>>tests/vhs_cassettes/MyAwesomeWikiClientTest_testSuccessSignUp.json<<<

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
