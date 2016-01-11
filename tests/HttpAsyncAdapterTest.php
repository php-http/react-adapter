<?php

namespace Http\Adapter\React\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\React\Client;

/**
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class HttpAsyncAdapterTest extends HttpAsyncClientTest
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
