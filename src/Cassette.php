<?php namespace korchasa\Vhs;

use http\Env\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cassette
{
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
    protected $dir;

    /**
     * @var string
     */
    protected $name;

    public function __construct(string $dir, string $name)
    {
        $this->dir = $dir;
        $this->name = $name;
    }

    /**
     * @param RequestInterface $request
     * @return Cassette
     */
    public function setRequest(RequestInterface $request): Cassette
    {
        $json = json_decode($request->getBody()->getContents());
        $this->record['request'] = [
            'uri' => (string) $request->getUri(),
            'method' => $request->getMethod(),
            'body_format' => $json ? 'json' : 'raw',
            'headers' => $request->getHeaders(),
            'body' => $json ?: $request->getBody()->getContents()
        ];
        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return Cassette
     */
    public function setResponse(ResponseInterface $response): Cassette
    {
        $json = json_decode($response->getBody()->getContents());
        $this->record['response'] = [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body_format' => $json ? 'json' : 'raw',
            'body' => $json ?: $response->getBody()->getContents()
        ];
        return $this;
    }

    public function buildResponse()
    {
        return new \GuzzleHttp\Psr7\Response(
            $this->record['response']['status'] ?? 200,
            $this->record['response']['headers'] ?? [],
            $this->record['response']['body_format'] == 'json'
                ? json_encode($this->record['response']['body'] ?? null)
                : $this->record['response']['body'] ?? null
        );
    }

    public function getRecord(): string
    {
        return json_encode($this->record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function isEmpty(): bool
    {
        return !file_exists($this->path());
    }

    public function save()
    {
        file_put_contents($this->path(), $this->getRecord());
    }

    public function load()
    {
        $this->record = json_decode($this->readRawRecord(), true);
    }

    public function path(): string
    {
        return $this->dir.DIRECTORY_SEPARATOR.$this->name.'.json';
    }

    public function readRawRecord()
    {
        return file_get_contents($this->path());
    }
}
