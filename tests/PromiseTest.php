<?php

namespace Http\Adapter\React\Tests;

use Http\Adapter\React\Exception\UnexpectedValueException;
use Http\Adapter\React\Promise;
use Http\Adapter\React\ReactFactory;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\NetworkException;
use Http\Client\Exception\TransferException;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\Promise as ReactPromise;
use RuntimeException;

class PromiseTest extends TestCase
{
    private $loop;

    public function setUp(): void
    {
        $this->loop = ReactFactory::buildEventLoop();
    }

    public function testChain()
    {
        $factory = new Psr17Factory();
        $request = $factory->createRequest('GET', 'http://example.org');
        $response = $factory->createResponse(200, "I'm OK…");

        $reactPromise = new ReactPromise(function ($resolve, $reject) use ($response) {
            $resolve($response);
        });

        $promise = new Promise($reactPromise, $this->loop, $request);

        $lastPromise = $promise->then(function (ResponseInterface $response) use ($factory) {
            return $factory->createResponse(300, $response->getReasonPhrase());
        });

        $updatedResponse = $lastPromise->wait();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(300, $updatedResponse->getStatusCode());
        self::assertEquals("I'm OK…", $updatedResponse->getReasonPhrase());
    }

    /**
     * @dataProvider exceptionThatIsThrownFromReactProvider
     */
    public function testPromiseExceptionsAreTranslatedToHttplug(
        RequestInterface $request,
        $reason,
        string $adapterExceptionClass
    ) {
        $reactPromise = new ReactPromise(function ($resolve, $reject) use ($reason) {
            $reject($reason);
        });

        $promise = new Promise($reactPromise, $this->loop, $request);
        $this->expectException($adapterExceptionClass);
        $promise->wait();
    }

    public function exceptionThatIsThrownFromReactProvider()
    {
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();

        return [
            'string'                   => [$request, 'whatever', UnexpectedValueException::class],
            'InvalidArgumentException' => [$request, new InvalidArgumentException('Something went wrong'), TransferException::class],
            'RuntimeException'         => [$request, new RuntimeException('Something happened inside ReactPHP engine'), NetworkException::class],
            'NetworkException'         => [$request, new NetworkException('Something happened inside ReactPHP engine', $request), NetworkException::class],
            'HttpException'            => [$request, new HttpException('Something happened inside ReactPHP engine', $request, $response), HttpException::class],
        ];
    }
}
