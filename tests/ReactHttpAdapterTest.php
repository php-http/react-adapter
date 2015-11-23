<?php

namespace Http\Adapter\Tests;

use Http\Discovery\MessageFactory;
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
        $messageFactory = new MessageFactory\GuzzleFactory();
        return new ReactHttpAdapter($messageFactory);
    }
}
