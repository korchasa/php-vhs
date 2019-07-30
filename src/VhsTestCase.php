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
    /** @var bool */
    protected $offline;
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
     * @param bool $offline
     * @param Config|null $config
     * @return Client
     */
    protected function connectVhs(Client $client, bool $offline = false, Config $config = null): Client
    {
        $this->offline = $offline;
        $this->vhsConfig = $config ?: new Config();
        $guzzleConfig = $client->getConfig();
        $guzzleConfig['handler'] = $this->connectToStack($guzzleConfig['handler']);
        return new Client($guzzleConfig);
    }

    private function connectToStack(HandlerStack $stack): HandlerStack
    {
        $stack->before('http_errors', Middleware::mapRequest(function (RequestInterface $request) {
            if ($this->offline && $this->currentVhsCassette->isSaved()) {
                return $this->createNullRequest();
            }
            $record = new Record();
            $record->request = $request;
            $this->currentVhsCassette->addRecord($record);
            return $request;
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            if ($this->offline && $this->currentVhsCassette->isSaved()) {
                $record = $this->currentVhsCassette->load()->records()[0];
                return $record->response;
            } else {
                $this->currentVhsCassette->modifyLastRecord(
                    function (Record $record) use ($response) {
                        $record->response = $response;

                        return $record;
                    }
                );
                return $response;
            }
        }));

        return $stack;
    }

    /**
     * @param callable $test
     * @param string $cassetteName
     * @return string Cassette name
     */
    protected function assertVhs(callable $test, string $cassetteName = null): string
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $cassetteName = $cassetteName ?: $this->vhsConfig->resolveCassetteName($caller['class'], $caller['function']);
        $cassettePath = $this->vhsConfig->resolveCassettePath($caller['class'], $cassetteName);
        $currentCassette = new Cassette($cassettePath);
        $this->currentVhsCassette = $currentCassette;
        $test();

        if (!$currentCassette->isSaved()) {
            $currentCassette->save();
            throw new IncompleteTestError("New cassette {$currentCassette->path()} recorded!");
        }

        $oldCassette = (new Cassette($currentCassette->path()))->load();
        $constraint = new JsonConstraint($oldCassette->getSerialized());
        static::assertThat($this->currentVhsCassette->getSerialized(), $constraint);

        return $cassetteName;
    }

    public function createNullRequest(): Request
    {
        return new Request('get', 'http://google.com/');
    }
}
