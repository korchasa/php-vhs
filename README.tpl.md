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
>>>tests/MyAwesomeApiClientTest.php<<<
```

Cassette ``tests/vhs_cassettes/MyAwesomeApiClientTest_testSuccessSignUp.json`` content
```json
>>>tests/vhs_cassettes/MyAwesomeApiClientTest_testSuccessSignUp.json<<<
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
