<?php

namespace Http\Adapter\Tests;

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
        $messageFactory = new \Http\Discovery\MessageFactory\GuzzleFactory();
        return new ReactHttpAdapter($messageFactory);
    }
}
