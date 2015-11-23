<?php

namespace Http\Adapter\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Tests\HttpClientTest;
use Http\Adapter\ReactHttpAdapter;

/**
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactHttpClientTest extends HttpClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAdapter()
    {
        $messageFactory = MessageFactoryDiscovery::find();
        return new ReactHttpAdapter($messageFactory);
    }
}
