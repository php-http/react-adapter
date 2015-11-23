<?php

namespace Http\Adapter;

use React\HttpClient\Response as ReactResponse;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Message Factory decorator to handle React response
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactMessageFactory
{
    /**
     * @param MessageFactory $factory
     */
    public function __construct(MessageFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Transform a React Response to a valid PSR7 ResponseInterface instance
     * @param  ReactResponse $response
     * @return ResponseInterface
     */
    public function buildResponse(
        ReactResponse $response,
        StreamInterface $body
    ) {
        $body->rewind();
        return $this->factory->createResponse(
            $response->getCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            $body,
            $response->getVersion()
        );
    }
}
