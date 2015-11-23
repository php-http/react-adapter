<?php

namespace Http\Adapter\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\ReactHttpAdapter;

/**
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactHttpAsyncClientTest extends HttpAsyncClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAsyncClient()
    {
        $messageFactory = MessageFactoryDiscovery::find();
        return new ReactHttpAdapter($messageFactory);
    }
}
