<?php namespace korchasa\Vhs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use korchasa\matched\JsonConstraint;
use PHPUnit\Framework\IncompleteTestError;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

trait VhsTestCase
{
    /**
     * @var string
     */
    protected $vhsCassettesDir;

    /**
     * @var Cassette
     */
    protected $currentVhsCassette;

    /**
     * @var bool
     */
    protected $testServer = false;

    protected function connectVhs(Client $client, string $dir = null): Client
    {
        $this->useVhsCassettesFrom($this->resolveCassettesDir($dir));
        $config = $client->getConfig();
        $config['handler'] = $this->connectToStack($config['handler']);
        return new Client($config);
    }

    private function connectToStack(HandlerStack $stack): HandlerStack
    {
        $stack->before('http_errors', Middleware::mapRequest(function (RequestInterface $request) {
            $this->currentVhsCassette->setRequest($request);
            if (!$this->testServer && $this->currentVhsCassette->exist()) {
                return $this->createNullRequest();
            } else {
                return $request;
            }
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->currentVhsCassette->setResponse($response);
            if (!$this->testServer && $this->currentVhsCassette->exist()) {
                return $this->currentVhsCassette->load()->buildGuzzleResponse();
            } else {
                return $response;
            }
        }));

        return $stack;
    }

    protected function assertVhs(callable $test, string $cassette = null)
    {
        $cassette = $cassette
            ?: $this->resolveCassette(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]);
        $this->currentVhsCassette = new Cassette($this->vhsCassettesDir, $cassette);
        $oldCassette = new Cassette($this->vhsCassettesDir, $cassette);
        $test();

        if (!$oldCassette->exist()) {
            $this->currentVhsCassette->save();
            throw new IncompleteTestError("New cassette {$this->currentVhsCassette->path()} recorded!");
        } else {
            $oldCassette->load();
            if (!$this->testServer) {
                $constraint = new JsonConstraint($oldCassette->getRequestJson());
                static::assertThat($this->currentVhsCassette->getRequestJson(), $constraint);
            } else {
                $constraint = new JsonConstraint($oldCassette->getAllRecordJson());
                static::assertThat($this->currentVhsCassette->getAllRecordJson(), $constraint);
            }
        }
    }

    private function resolveCassette($callFromTest): string
    {
        return substr($callFromTest['class'], strrpos($callFromTest['class'], '\\') + 1) .'_'.$callFromTest['function'];
    }

    public function createNullRequest()
    {
        return new Request('get', 'http://google.com/');
    }

    public function useVhsCassettesFrom($dir)
    {
        $this->vhsCassettesDir = $dir;
        return $this;
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
