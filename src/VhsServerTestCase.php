<?php declare(strict_types=1);

namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

trait VhsServerTestCase
{
    /** @var Config */
    protected $vhsConfig;

    public function assertValidServerResponse(string $cassettesPathPattern)
    {
        if (null == $this->vhsConfig) {
            $this->vhsConfig = new Config();
        }

        foreach ($this->findCassettes($cassettesPathPattern) as $cassette) {
            $cassette->loadRecord();
            $this->assertValidServerResponseForCassette($cassette);
        }
    }

    public function assertValidServerResponseForCassette(Cassette $cassette)
    {
        $actualResponse = (new Client())->send($cassette->buildPsrRequest());
        self::assertThat($actualResponse->getBody()->getContents(), $cassette->bodyConstraint());
    }

    /**
     * @param string $cassettesPathPattern
     * @return Cassette[]
     */
    protected function findCassettes(string $cassettesPathPattern): array
    {
        return array_map(function ($cassettePath) {
            return new Cassette($cassettePath, str_replace('.json', '', basename($cassettePath)));
        }, glob($cassettesPathPattern));
    }

    protected function buildRequest(Cassette $cassette): Request
    {
        $requestParams = json_decode($cassette->getRequestJson());

        return new Request(
            $requestParams->method,
            $requestParams->uri,
            (array) $requestParams->headers,
            $requestParams->body
        );
    }
}
