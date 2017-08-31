<?php

namespace Http\Adapter\React;

use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\RequestException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\ResponseFactory;
use Http\Message\StreamFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client as ReactClient;
use React\HttpClient\Request as ReactRequest;
use React\HttpClient\Response as ReactResponse;

/**
 * Client for the React promise implementation.
 *
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class Client implements HttpClient, HttpAsyncClient
{
    /**
     * React HTTP client.
     *
     * @var Client
     */
    private $client;

    /**
     * React event loop.
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * Initialize the React client.
     *
     * @param ResponseFactory|null $responseFactory
     * @param LoopInterface|null   $loop
     * @param ReactClient|null     $client
     * @param StreamFactory|null   $streamFactory
     */
    public function __construct(
        ResponseFactory $responseFactory = null,
        LoopInterface $loop = null,
        ReactClient $client = null,
        StreamFactory $streamFactory = null
    ) {
        if (null !== $client && null === $loop) {
            throw new \RuntimeException(
                'You must give a LoopInterface instance with the Client'
            );
        }

        $this->loop = $loop ?: ReactFactory::buildEventLoop();
        $this->client = $client ?: ReactFactory::buildHttpClient($this->loop);

        $this->responseFactory = $responseFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        $promise = $this->sendAsyncRequest($request);

        return $promise->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        $reactRequest = $this->buildReactRequest($request);
        $promise = new Promise($this->loop);

        $reactRequest->on('error', function (\Exception $error) use ($promise, $request) {
            $promise->reject(new RequestException(
                $error->getMessage(),
                $request,
                $error
            ));
        });

        $reactRequest->on('response', function (ReactResponse $reactResponse = null) use ($promise, $request) {
            $bodyStream = $this->streamFactory->createStream();
            $reactResponse->on('data', function ($data) use (&$bodyStream) {
                $bodyStream->write((string) $data);
            });

            $reactResponse->on('end', function (\Exception $error = null) use ($promise, $request, $reactResponse, &$bodyStream) {
                $response = $this->buildResponse(
                    $reactResponse,
                    $bodyStream
                );
                if (null !== $error) {
                    $promise->reject(new HttpException(
                        $error->getMessage(),
                        $request,
                        $response,
                        $error
                    ));
                } else {
                    $promise->resolve($response);
                }
            });
        });

        $reactRequest->end((string) $request->getBody());

        return $promise;
    }

    /**
     * Build a React request from the PSR7 RequestInterface.
     *
     * @param RequestInterface $request
     *
     * @return ReactRequest
     */
    private function buildReactRequest(RequestInterface $request)
    {
        $headers = [];

        foreach ($request->getHeaders() as $name => $value) {
            $headers[$name] = (is_array($value) ? $value[0] : $value);
        }

        $reactRequest = $this->client->request(
            $request->getMethod(),
            (string) $request->getUri(),
            $headers,
            $request->getProtocolVersion()
        );

        return $reactRequest;
    }

    /**
     * Transform a React Response to a valid PSR7 ResponseInterface instance.
     *
     * @param ReactResponse   $response
     * @param StreamInterface $body
     *
     * @return ResponseInterface
     */
    private function buildResponse(
        ReactResponse $response,
        StreamInterface $body
    ) {
        $body->rewind();

        return $this->responseFactory->createResponse(
            $response->getCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            $body,
            $response->getVersion()
        );
    }
}
