<?php declare(strict_types=1);

namespace korchasa\Vhs;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Converter
{
    public function serialize(Cassette $from): string
    {
        $recordsJson = array_map(function (Record $record) {
            return $this->serializeRecord($record);
        }, $from->records());
        return json_encode($recordsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function unserialize(string $json, Cassette $to): void
    {
        $recordsSpecs = json_decode($json, true);
        if (false === $recordsSpecs) {
            throw new \InvalidArgumentException("Can'r decode records: ".json_last_error_msg());
        }
        $records = array_map(function (array $record) {
            return $this->unserializeRecord($record);
        }, $recordsSpecs);

        foreach ($records as $record) {
            $to->addRecord($record);
        }
    }

    /**
     * @param Record $record
     * @return array
     */
    protected function serializeRecord(Record $record): array
    {
        $request = $record->request;
        $request->getBody()->rewind();
        $reqBody = $request->getBody()->getContents();
        $request->getBody()->rewind();
        $reqJson = json_decode($reqBody);

        $response = $record->response;
        $response->getBody()->rewind();
        $respBody = $response->getBody()->getContents();
        $response->getBody()->rewind();
        $respJson = json_decode($respBody);

        return [
            'request' => [
                'uri' => (string) $request->getUri(),
                'method' => $request->getMethod(),
                'headers' => $request->getHeaders(),
                'body' => $reqJson !== null ? $reqJson : $reqBody,
                'body_format' => $reqJson !== null ? 'json' : 'raw',
            ],
            'response' => [
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $respJson !== null ? $respJson : $respBody,
                'body_format' => $respJson !== null  ? 'json' : 'raw',
            ]
        ];
    }

    protected function unserializeRecord(array $json): Record
    {
//        var_dump($json);
        if (!isset($json['request'])) {
            throw new \InvalidArgumentException("Can't find request section in record");
        }
        $requestJson = $json['request'];
        $record = new Record();
        $record->request =  new Request(
            $requestJson['method'],
            $requestJson['uri'],
            $requestJson['headers'],
            ($requestJson['body_format'] ?? 'raw') === 'json'
                ? json_encode($requestJson['body'])
                : $requestJson['body']
        );

        $responseRecord = $json['response'];
        $record->response = new Response(
            $responseRecord['status'],
            $responseRecord['headers'],
            $responseRecord['body_format'] === 'json'
                ? json_encode($responseRecord['body'])
                : $responseRecord['body']
        );

        return $record;
    }
}
