<?php namespace korchasa\Vhs;

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
            if ($this->currentVhsCassette->exist()) {
                return $this->createNullRequest();
            }

            return $request;
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->currentVhsCassette->setResponse($response);
            if ($this->currentVhsCassette->exist()) {
                return $this->currentVhsCassette->load()->buildPsrResponse();
            }

            return $response;
        }));

        return $stack;
    }

    protected function assertVhs(callable $test, string $cassette = null)
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $cassette = $cassette ?: $this->vhsConfig->resolveCassetteName($caller['class'], $caller['function']);
        $cassettePath = $this->vhsConfig->resolveCassettePath($caller['class'], $cassette);
        $this->currentVhsCassette = new Cassette($cassettePath, $cassette);
        $oldCassette = new Cassette($cassettePath, $cassette);
        $test();

        if (!$oldCassette->exist()) {
            $this->currentVhsCassette->save();
            throw new IncompleteTestError("New cassette {$this->currentVhsCassette->path()} recorded!");
        }

        $oldCassette->load();
        $constraint = new JsonConstraint($oldCassette->getRequestJson());
        static::assertThat($this->currentVhsCassette->getRequestJson(), $constraint);
    }

    private function resolveCassetteName($callFromTest): string
    {
        return substr($callFromTest['class'], strrpos($callFromTest['class'], '\\') + 1) .'_'.$callFromTest['function'];
    }

    public function createNullRequest()
    {
        return new Request('get', 'http://google.com/');
    }
}
