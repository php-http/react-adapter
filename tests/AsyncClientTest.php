<?php

namespace Http\Adapter\React\Tests;

use Http\Client\HttpClient;
use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\React\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;

/**
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class AsyncClientTest extends HttpAsyncClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAsyncClient()
    {
        return new Client(new GuzzleMessageFactory());
    }
}
