<?php declare(strict_types=1);

namespace korchasa\Vhs\Tests;

use korchasa\Vhs\Cassette;
use korchasa\Vhs\Converter;

class ConverterTest extends TestCase
{
    public function testSerializeUnserialize(): void
    {
        $converter = new Converter();
        $cassetteFrom = new Cassette('some_path', [$record = $this->generateRecord()]);
        $rawJson = $converter->serialize($cassetteFrom);
        $cassetteTo = new Cassette($cassetteFrom->path());
        $converter->unserialize($rawJson, $cassetteTo);

        $expectedReq = $cassetteFrom->records()[0]->request;
        $actualReq = $cassetteTo->records()[0]->request;
        $this->assertEquals($expectedReq->getUri(), $actualReq->getUri());
        $this->assertEquals($expectedReq->getMethod(), $actualReq->getMethod());
        $this->assertEquals($expectedReq->getHeaders(), $actualReq->getHeaders());
        $this->assertEquals($expectedReq->getBody()->getContents(), $actualReq->getBody()->getContents());

        $expectedResp = $cassetteFrom->records()[0]->response;
        $actualResp = $cassetteTo->records()[0]->response;
        $this->assertEquals($expectedResp->getStatusCode(), $actualResp->getStatusCode());
        $this->assertEquals($expectedResp->getHeaders(), $actualResp->getHeaders());
        $this->assertEquals($expectedResp->getBody()->getContents(), $actualResp->getBody()->getContents());
    }
}
