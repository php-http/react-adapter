<?php

namespace Http\Adapter\React\Tests;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\React\Client;

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
        $messageFactory = MessageFactoryDiscovery::find();
        return new Client($messageFactory);
    }
}
