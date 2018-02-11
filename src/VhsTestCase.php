<?php declare(strict_types=1);

namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use korchasa\matched\JsonConstraint;
use PHPUnit\Framework\IncompleteTestError;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait VhsTestCase
{
    /**
     * @var Config
     */
    protected $vhsConfig;

    /**
     * @var Cassette
     */
    protected $currentVhsCassette;

    /**
     * @param Client $client
     * @param Config|null $config
     * @return Client
     */
    protected function connectVhs(Client $client, Config $config = null): Client
    {
        $this->vhsConfig = $config ?: new Config();
        $guzzleConfig = $client->getConfig();
        $guzzleConfig['handler'] = $this->connectToStack($guzzleConfig['handler']);
        return new Client($guzzleConfig);
    }

    private function connectToStack(HandlerStack $stack): HandlerStack
    {
        $stack->before('http_errors', Middleware::mapRequest(function (RequestInterface $request) {
            $this->currentVhsCassette->setRequest($request);
            if ($this->currentVhsCassette->haveRecord()) {
                return $this->createNullRequest();
            }

            return $request;
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->currentVhsCassette->setResponse($response);
            if ($this->currentVhsCassette->haveRecord()) {
                return $this->currentVhsCassette->loadRecord()->buildPsrResponse();
            }

            return $response;
        }));

        return $stack;
    }

    /**
     * @param callable $test
     * @param string|null $cassetteName
     * @return string Cassette name
     * @throws \ReflectionException
     */
    protected function assertVhs(callable $test, string $cassetteName = null): string
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $cassetteName = $cassetteName ?: $this->vhsConfig->resolveCassetteName($caller['class'], $caller['function']);
        $cassettePath = $this->vhsConfig->resolveCassettePath($caller['class'], $cassetteName);
        $this->currentVhsCassette = new Cassette($cassettePath, $cassetteName);
        $oldCassette = new Cassette($cassettePath, $cassetteName);
        $test();

        if (!$oldCassette->haveRecord()) {
            $this->currentVhsCassette->saveRecord();
            throw new IncompleteTestError("New cassette {$this->currentVhsCassette->path()} recorded!");
        }

        $oldCassette->loadRecord();
        $constraint = new JsonConstraint($oldCassette->getRequestJson());
        static::assertThat($this->currentVhsCassette->getRequestJson(), $constraint);

        return $cassetteName;
    }

    public function createNullRequest(): Request
    {
        return new Request('get', 'http://google.com/');
    }
}
