<?php

namespace Monero;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class jsonRpcBase
{
    /** @var string */
    private $username = 'test2';

    /** @var string */
    private $password = 'test2';

    /** @var string  */
    private $url = 'http://127.0.0.1:28080/json_rpc';

    /** @var Client|null  */
    private $client;
    private $auth_type;

    /**
     * JsonRPCClient constructor.
     * @param array $options
     * @param null $client
     */
    public function __construct($options, $client = null)
    {
        $this->username = $options['username'] ?? $this->username;
        $this->password = $options['password'] ?? $this->password;
        $this->url = $options['url'] ?? $this->url;
        $this->auth_type = $options['auth_type'] ?? 'digest';

        if (empty($client)) {
            $client = new Client([
                'base_uri' => $this->url,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);
        }

        $this->client = $client;
    }

    /**
     * Sets up the request data body
     *
     * @param string    $method name of the rpc command
     * @param array     $params associative array of variables being passed to the method
     *
     * @return false|string will return a json string or false
     */
    private function preparePayload($method, $params)
    {
        $payload = [
            'jsonrpc' => '2.0',
            'id' => '0',
            'method' => $method,
            'params' => $params,
        ];
        return json_encode($payload);
    }

    /**
     * Send off request to rpc server
     *
     * @param string    $method name of the rpc command
     * @param array     $params associative array of variables being passed to the method
     *
     * @return mixed the rpc query result
     *
     * @throws \RuntimeException
     */
    public function request(string $method, array $params = [])
    {
        $payload = $this->preparePayload($method, $params);

        try {
            $response = $this->client->request('POST', '',[
                'auth' => [$this->username, $this->password, $this->auth_type],
                'body' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $body = $response->getBody();
        } catch (GuzzleException $exception) {
            Log::error($exception);
            error_log($exception);
            throw new \RuntimeException('Connection to node ' . $this->url . ' unsuccessful');
        }
        $result = json_decode((string) $body, true);
        if (isset($result['error'])) {

            throw new \RuntimeException($result['error']['message']);
        }
        return $result['result'];
    }

}
