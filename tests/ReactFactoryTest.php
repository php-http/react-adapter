<?php

namespace Http\Adapter\React\Tests;

use Http\Adapter\React\ReactFactory;
use PHPUnit\Framework\TestCase;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\Socket\ConnectorInterface;

/**
 * These tests don't really ensure the correct instances are baked into the returned http client, instead, they are
 * just testing the code against the expected use cases.
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class ReactFactoryTest extends TestCase
{
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    protected function setUp()
    {
        $this->loop = $this->createMock(LoopInterface::class);
    }

    public function testBuildHttpClientWithConnector()
    {
        $client = ReactFactory::buildHttpClient($this->loop, $this->createMock(ConnectorInterface::class));
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @deprecated Building HTTP client passing a DnsResolver instance is deprecated. Should pass a ConnectorInterface
     *             instance instead.
     */
    public function testBuildHttpClientWithDnsResolver()
    {
        $client = ReactFactory::buildHttpClient($this->loop, $this->createMock(Resolver::class));
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildHttpClientWithoutConnector()
    {
        $client = ReactFactory::buildHttpClient($this->loop);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildHttpClientWithInvalidConnectorThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        ReactFactory::buildHttpClient($this->loop, $this->createMock(LoopInterface::class));
    }
}
