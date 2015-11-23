<?php

namespace Http\Adapter\Tests;

use Http\Client\Tests\HttpClientTest;
use Http\Adapter\ReactHttpClient;

/**
 * @author Stéphane Hulard <s.hulard@gmail.com>
 */
class ReactHttpClientTest extends HttpClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAdapter()
    {
        $messageFactory = new \Http\Discovery\MessageFactory\GuzzleFactory();
        return new ReactHttpClient($messageFactory);
    }
}
