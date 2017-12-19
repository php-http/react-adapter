<?php

namespace Http\Adapter\React\Tests;

use Http\Adapter\React\ReactFactory;
use PHPUnit\Framework\TestCase;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Factory;
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
        $this->loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    }

    public function testBuildHttpClientWithConnector()
    {
        if (class_exists(Factory::class)) {
            $this->markTestSkipped('This test only runs with react http client v0.5 and above');
        }

        $connector = $this->getMockBuilder(ConnectorInterface::class)->getMock();
        $client = ReactFactory::buildHttpClient($this->loop, $connector);
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @deprecated Building HTTP client passing a DnsResolver instance is deprecated. Should pass a ConnectorInterface
     *             instance instead.
     */
    public function testBuildHttpClientWithDnsResolver()
    {
        $connector = $this->getMockBuilder(Resolver::class)->disableOriginalConstructor()->getMock();
        $client = ReactFactory::buildHttpClient($this->loop, $connector);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildHttpClientWithoutConnector()
    {
        $client = ReactFactory::buildHttpClient($this->loop);
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildHttpClientWithInvalidConnectorThrowsException()
    {
        $connector = $this->getMockBuilder(LoopInterface::class)->getMock();
        ReactFactory::buildHttpClient($this->loop, $connector);
    }
}
