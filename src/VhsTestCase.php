<?php namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
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

    protected function connectVhs(Client $client)
    {
        $config = $client->getConfig();
        $config['handler'] = $this->connectToStack($config['handler']);

//        return function (callable $handler) {
//            return function (
//                RequestInterface $request,
//                array $options
//            ) use ($handler, $header, $value) {
//                $request = $request->withHeader($header, $value);
//                return $handler($request, $options);
//            };
//        };

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
        $cassette = new Cassette($this->cassettesDir, $cassette)
        if (!$this->isCassetteExist($cassette)) {
            $this->currentCassette = new Cassette($isEmpty = true);
        }
        $test();
        var_dump($this->currentCassette->toText());
    }

    protected function useVhsCassettesFrom($dir)
    {
        $this->cassettesDir = $dir;
    }

    private function isCassetteExist($name)
    {
        foreach ($this->)
        return false;
    }
}
