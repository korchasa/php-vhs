<?php namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use korchasa\matched\JsonConstraint;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait VhsTestCase
{
    /**
     * @var string[]
     */
    protected $cassettesDir;

    /**
     * @var Cassette
     */
    protected $currentCassette;

    protected function connectVhs(string $dir, Client $client)
    {
        $this->useVhsCassettesFrom($dir);
        $config = $client->getConfig();
        $config['handler'] = $this->connectToStack($config['handler']);
        return new Client($config);
    }

    private function connectToStack(HandlerStack $stack)
    {
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            $this->currentCassette->setRequest($request);
            return $request;
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->currentCassette->setResponse($response);
            return $response;
        }));

        return $stack;
    }

    protected function assertVhs(string $cassette, callable $test)
    {
        $this->currentCassette = new Cassette($this->cassettesDir, $cassette);
        $oldCassette = new Cassette($this->cassettesDir, $cassette);
        $test();
        if ($oldCassette->isEmpty()) {
            $this->currentCassette->save();
        } else {
            $constraint = new JsonConstraint($oldCassette->readRecord());
            static::assertThat($this->currentCassette->getRecord(), $constraint);
        }
    }

    protected function useVhsCassettesFrom($dir)
    {
        $this->cassettesDir = $dir;
    }
}
