<?php

namespace Http\Adapter\React\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Tests\HttpClientTest;
use Http\Adapter\React\Client;

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
        $messageFactory = MessageFactoryDiscovery::find();
        return new Client($messageFactory);
    }
}
