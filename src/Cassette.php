<?php namespace korchasa\Vhs;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cassette
{
    /**
     * @var string
     */
    private $file;

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

    public function __construct(string $dir, string $name, bool $isEmpty)
    {
        $this->isEmpty = $isEmpty;
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

    public function toText()
    {
        return json_encode([
            'request' => [
                'uri' => (string) $this->request->getUri()
            ],
            'response' => [
                'status' => $this->response->getStatusCode()
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
