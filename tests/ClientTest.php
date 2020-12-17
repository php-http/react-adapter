<?php

namespace Http\Adapter\React\Tests;

use Http\Client\Tests\HttpClientTest;
use Http\Adapter\React\Client;
use Psr\Http\Client\ClientInterface;

/**
 * @author Stéphane Hulard <s.hulard@chstudio.fr>
 */
class ClientTest extends HttpClientTest
{
    protected function createHttpAdapter(): ClientInterface
    {
        return new Client();
    }
}
