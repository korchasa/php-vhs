<?php declare(strict_types=1);

namespace korchasa\Vhs;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Record
{
    /** @var RequestInterface */
    public $request;
    /** @var ResponseInterface */
    public $response;

    public function __construct(RequestInterface $req = null, ResponseInterface $resp = null)
    {
        $this->request = $req;
        $this->response = $resp;
    }
}
