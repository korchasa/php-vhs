<?php namespace korchasa\Vhs;

use GuzzleHttp\Client;

trait VhsTestCase
{
    protected function connectVhs(Client $client)
    {
        return new Client();

//        return function (callable $handler) {
//            return function (
//                RequestInterface $request,
//                array $options
//            ) use ($handler, $header, $value) {
//                $request = $request->withHeader($header, $value);
//                return $handler($request, $options);
//            };
//        };
    }

    protected function assertVhs(callable $test, string $cassette = null)
    {

    }

    protected function useVhsCassettesFrom(string ...$dirs)
    {

    }
}