<?php

namespace Http\Adapter\React\Tests;

use Http\Client\Tests\HttpAsyncClientTest;
use Http\Adapter\React\Client;
use Http\Client\HttpAsyncClient;

/**
 * @author Stéphane Hulard <s.hulard@chstudio.fr>
 */
class AsyncClientTest extends HttpAsyncClientTest
{
    protected function createHttpAsyncClient(): HttpAsyncClient
    {
        return new Client();
    }
}
