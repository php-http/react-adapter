<?php

namespace Http\Adapter\React\Tests;

use GuzzleHttp\Psr7\Response;
use Http\Adapter\React\Promise;
use Http\Adapter\React\ReactFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

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

    public function testOnFulfilledOptionalReturn()
    {
        $promise = new Promise($this->loop);
        $response = new Response(200);

        // create a random mock so we can assert $onFulfilled is called with the correct response
        /** @var \SplObjectStorage|\PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockBuilder(\SplObjectStorage::class)->getMock();
        $mock->expects(self::once())->method('attach')->with($response);

        $lastPromise = $promise->then(function (ResponseInterface $response) use ($mock) {
            $mock->attach($response);
        });

        $promise->resolve($response);
        $lastResponse = $lastPromise->wait();

        // even though our $onFulfilled doesn't return a value, we expect the promise to unwrap the original response
        self::assertSame($response, $lastResponse);
    }
}
