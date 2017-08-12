<?php namespace korchasa\Vhs;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cassette
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;
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
        $this->request = $request;
        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return Cassette
     */
    public function setResponse(ResponseInterface $response): Cassette
    {
        $this->response = $response;
        return $this;
    }

    public function getRecord($withHeaders = false): string
    {
        $record = [
            'request' => [
                'uri' => (string) $this->request->getUri(),
                'method' => $this->request->getMethod(),
                'body' => json_decode($this->request->getBody())
                            ?: $this->request->getBody()
            ],
            'response' => [
                'status' => $this->response->getStatusCode(),
                'body' => json_decode($this->response->getBody()->getContents())
                            ?: $this->response->getBody()->getContents()
            ]
        ];

        if ($withHeaders) {
            $record['request']['headers'] = $this->request->getHeaders();
            $record['response']['headers'] = $this->response->getHeaders();
        }

        return json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function isEmpty(): bool
    {
        return !file_exists($this->path());
    }

    public function save()
    {
        file_put_contents($this->path(), $this->getRecord());
    }

    public function path(): string
    {
        return $this->dir.DIRECTORY_SEPARATOR.$this->name.'.json';
    }

    public function readRecord()
    {
        return file_get_contents($this->path());
    }
}
