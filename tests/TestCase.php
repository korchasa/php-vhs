<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use korchasa\Vhs\Record;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function generateRecord(): Record
    {
        return new Record(
            new Request(
                'post',
                'https://httpbin.org/get?ffoo=bbar',
                ['someHeader' => 'value'],
                '{"foo":"bar"}'
            ),
            new Response(
                200,
                ['some' => 'value'],
                '{"args":[],"url":"https:\/\/httpbin.org\/get"}'
            )
        );
    }
}
