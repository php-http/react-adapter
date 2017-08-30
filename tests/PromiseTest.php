<?php

namespace Http\Adapter\React\Tests;

use GuzzleHttp\Psr7\Response;
use Http\Adapter\React\Promise;
use Http\Adapter\React\ReactFactory;
use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;

class PromiseTest extends TestCase
{
    private $loop;

    public function setUp()
    {
        $this->loop = ReactFactory::buildEventLoop();
    }

    public function testChain()
    {
        $promise = new Promise($this->loop);
        $response = new Response(200);

        $lastPromise = $promise->then(function (Response $response) {
            return $response->withStatus(300);
        });

        $promise->resolve($response);
        $updatedResponse = $lastPromise->wait();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(300, $updatedResponse->getStatusCode());
    }
}
