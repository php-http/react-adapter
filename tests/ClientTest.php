<?php

namespace Http\Adapter\React\Tests;

use Http\Client\HttpClient;
use Http\Client\Tests\HttpClientTest;
use Http\Adapter\React\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;

/**
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ClientTest extends HttpClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAdapter()
    {
        return new Client(new GuzzleMessageFactory());
    }
}
