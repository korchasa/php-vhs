<?php namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use korchasa\matched\JsonConstraint;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

trait VhsTestCase
{
    /**
     * @var string
     */
    protected $cassettesDir;

    /**
     * @var Cassette
     */
    protected $currentCassette;

    protected function connectVhs(Client $client, string $dir = null): Client
    {
        $this->useVhsCassettesFrom($this->resolveCassettesDir($dir));
        $config = $client->getConfig();
        $config['handler'] = $this->connectToStack($config['handler']);
        return new Client($config);
    }

    private function connectToStack(HandlerStack $stack): HandlerStack
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

    protected function assertVhs(callable $test, string $cassette = null)
    {
        $cassette = $cassette
            ?: $this->resolveCassette(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]);
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

    private function resolveCassette($callFromTest): string
    {
        return substr($callFromTest['class'], strrpos($callFromTest['class'], '\\') + 1) .'_'.$callFromTest['function'];
    }

    public function useVhsCassettesFrom($dir)
    {
        $this->cassettesDir = $dir;
    }

    private function resolveCassettesDir($givenDir): string
    {
        $dir = $givenDir ?: getenv("VHS_DIR");
        if (!$dir) {
            $reflector = new ReflectionClass(get_called_class());
            $dir = dirname($reflector->getFileName()).'/vhs_cassettes';
        }
        return $dir;
    }
}
