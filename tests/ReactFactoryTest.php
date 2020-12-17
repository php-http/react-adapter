<?php

namespace Http\Adapter\React\Tests;

use Http\Adapter\React\ReactFactory;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
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

    protected function setUp(): void
    {
        $this->loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    }

    public function testBuildHttpClientWithConnector()
    {
        /** @var ConnectorInterface $connector */
        $connector = $this->getMockBuilder(ConnectorInterface::class)->getMock();
        $client = ReactFactory::buildHttpClient($this->loop, $connector);
        $this->assertInstanceOf(Browser::class, $client);
    }

    public function testBuildHttpClientWithoutConnector()
    {
        $client = ReactFactory::buildHttpClient($this->loop);
        $this->assertInstanceOf(Browser::class, $client);
    }
}
