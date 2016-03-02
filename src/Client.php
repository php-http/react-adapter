<?php

namespace Http\Adapter\React;

use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\HttpClient\Client as ReactClient;
use React\HttpClient\Request as ReactRequest;
use React\HttpClient\Response as ReactResponse;
use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\RequestException;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

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
     * HttpPlug message factory.
     *
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * Initialize the React client.
     *
     * @param MessageFactory     $messageFactory
     * @param LoopInterface|null $loop     React Event loop
     * @param ReactClient        $client   React client to use
     */
    public function __construct(
        MessageFactory $messageFactory,
        LoopInterface $loop = null,
        ReactClient $client = null
    ) {
        if (null !== $client && null === $loop) {
            throw new \RuntimeException(
                'You must give a LoopInterface instance with the Client'
            );
        }
        $this->loop = (null !== $loop) ?: ReactFactory::buildEventLoop();
        $this->client = (null !== $client) ?: ReactFactory::buildHttpClient($this->loop);

        $this->messageFactory = $messageFactory;
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
        $deferred = new Deferred();

        $reactRequest->on('error', function (\Exception $error) use ($deferred, $request) {
            $deferred->reject(new RequestException(
                $error->getMessage(),
                $request,
                $error
            ));
        });
        $reactRequest->on('response', function (ReactResponse $reactResponse = null) use ($deferred, $reactRequest, $request) {
            $bodyStream = null;
            $reactResponse->on('data', function ($data) use (&$bodyStream) {
                if ($data instanceof StreamInterface) {
                    $bodyStream = $data;
                } else {
                    $bodyStream->write($data);
                }
            });

            $reactResponse->on('end', function (\Exception $error = null) use ($deferred, $request, $reactResponse, &$bodyStream) {
                $bodyStream->rewind();
                $response = $this->buildResponse(
                    $reactResponse,
                    $bodyStream
                );
                if (null !== $error) {
                    $deferred->reject(new HttpException(
                        $error->getMessage(),
                        $request,
                        $response,
                        $error
                    ));
                } else {
                    $deferred->resolve($response);
                }
            });
        });

        $reactRequest->end((string) $request->getBody());

        $promise = new Promise($deferred->promise());
        $promise->setLoop($this->loop);

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
     * @param ReactResponse $response
     *
     * @return ResponseInterface
     */
    private function buildResponse(
        ReactResponse $response,
        StreamInterface $body
    ) {
        $body->rewind();

        return $this->messageFactory->createResponse(
            $response->getCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            $body,
            $response->getVersion()
        );
    }
}
