<?php

namespace Http\Adapter\React\Tests;

use Http\Client\HttpClient;
use Http\Client\Tests\HttpClientTest;
use Http\Adapter\React\Client;
use Http\Discovery\MessageFactoryDiscovery;

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
