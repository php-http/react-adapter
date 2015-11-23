<?php

namespace Http\Adapter\Tests;

use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\ReactHttpClient;

/**
 * @author Stéphane Hulard <s.hulard@gmail.com>
 */
class ReactHttpAsyncClientTest extends HttpAsyncClientTest
{
    /**
     * @return HttpClient
     */
    protected function createHttpAsyncClient()
    {
        $messageFactory = new \Http\Discovery\MessageFactory\GuzzleFactory();

        return new ReactHttpClient(
            $messageFactory
        );
    }
}
