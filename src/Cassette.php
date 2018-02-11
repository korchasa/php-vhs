<?php declare(strict_types=1);

namespace korchasa\Vhs;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use korchasa\matched\AssertMatchedTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cassette
{
    use AssertMatchedTrait;

    /**
     * @var ResponseInterface
     */
    protected $record;
    /**
     * @var bool
     */
    protected $isEmpty;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    protected $request;
    protected $response;

    public function __construct(string $path, string $name)
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * @param RequestInterface $request
     * @return Cassette
     */
    public function setRequest(RequestInterface $request): Cassette
    {
        $body = $request->getBody()->getContents();
        $request->getBody()->rewind();
        $json = json_decode($body);
        $this->record['request'] = [
            'uri' => (string) $request->getUri(),
            'method' => $request->getMethod(),
            'body_format' => $json ? 'json' : 'raw',
            'headers' => $request->getHeaders(),
            'body' => $json ?: $body
        ];
        return $this;
    }

    /**
     * @return Request
     */
    public function buildPsrRequest(): Request
    {
        return new Request(
            $this->record['request']['method'] ?? 'GET',
            $this->record['request']['uri'] ?? 'localhost',
            $this->record['request']['headers'] ?? [],
            $this->record['request']['body'] ?? ''
        );
    }

    /**
     * @param ResponseInterface $response
     * @return Cassette
     */
    public function setResponse(ResponseInterface $response): Cassette
    {
        $body = $response->getBody()->getContents();
        $response->getBody()->rewind();
        $json = json_decode($body);
        $this->record['response'] = [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body_format' => null === $json  ? 'json' : 'raw',
            'body' => $json ?: $body
        ];
        return $this;
    }

    /**
     * @return Response
     */
    public function buildPsrResponse(): Response
    {
        return new Response(
            $this->record['response']['status'] ?? 200,
            $this->record['response']['headers'] ?? [],
            $this->record['response']['body_format'] === 'json'
                ? json_encode($this->record['response']['body'] ?? null)
                : $this->record['response']['body'] ?? null
        );
    }

    public function getAllRecordJson(): string
    {
        return $this->encode($this->record);
    }

    public function getRequestJson(): string
    {
        return $this->encode($this->record['request']);
    }

    /**
     * @return BodyConstraint
     */
    public function bodyConstraint(): BodyConstraint
    {
        if ('json' === $this->record['response']['body_format']) {
            return new BodyConstraint($this->record['response']['body'], true);
        }

        return new BodyConstraint($this->record['response']['body'], false);
    }

    public function exist(): bool
    {
        return file_exists($this->path());
    }

    public function save()
    {
        file_put_contents($this->path(), $this->getAllRecordJson());
    }

    public function load()
    {
        $this->record = $this->decode(file_get_contents($this->path()));
        return $this;
    }

    public function path(): string
    {
        return $this->path;
    }

    private function encode($mixed): string
    {
        return json_encode($mixed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function decode(string $json)
    {
        return json_decode($json, true);
    }
}
