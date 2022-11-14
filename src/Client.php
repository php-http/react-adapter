<?php

namespace Http\Adapter\React;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser as ReactBrowser;

/**
 * Client for the React promise implementation.
 *
 * @author Stéphane Hulard <s.hulard@chstudio.fr>
 */
class Client implements HttpClient, HttpAsyncClient
{
    /**
     * React HTTP client.
     *
     * @var ReactBrowser
     */
    private $client;

    /**
     * Initialize the React client.
     */
    public function __construct(
        ReactBrowser $client = null
    ) {
        $this->client = $client ?: ReactFactory::buildHttpClient();
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $promise = $this->sendAsyncRequest($request);

        // The promise is declared to return mixed, but the react client promise returns a response.
        // We unwrap the exception if there is any, otherwise the promise would return null on error.
        return $promise->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        $promise = new Promise(
            $this->client->request(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders(),
                $request->getBody()
            ),
            $request
        );

        return $promise;
    }
}
