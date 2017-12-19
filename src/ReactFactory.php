<?php

namespace Http\Adapter\React;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Dns\Resolver\Resolver as DnsResolver;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\HttpClient\Client as HttpClient;
use React\Socket\Connector;
use React\Socket\ConnectorInterface;

/**
 * Factory wrapper for React instances.
 *
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactFactory
{
    /**
     * Build a react Event Loop.
     *
     * @return LoopInterface
     */
    public static function buildEventLoop()
    {
        return EventLoopFactory::create();
    }

    /**
     * Build a React Dns Resolver.
     *
     * @param LoopInterface $loop
     * @param string        $dns
     *
     * @return DnsResolver
     */
    public static function buildDnsResolver(
        LoopInterface $loop,
        $dns = '8.8.8.8'
    ) {
        $factory = new DnsResolverFactory();

        return $factory->createCached($dns, $loop);
    }

    /**
     * @param LoopInterface    $loop
     * @param DnsResolver|null $dns
     *
     * @return ConnectorInterface
     */
    public static function buildConnector(
        LoopInterface $loop,
        DnsResolver $dns = null
    ) {
        if (null === $dns) {
            $dns = self::buildDnsResolver($loop);
        }

        return new Connector($loop, ['dns' => $dns]);
    }

    /**
     * Build a React Http Client.
     *
     * @param LoopInterface                  $loop
     * @param ConnectorInterface|DnsResolver $connector Passing a DnsResolver instance is deprecated. Should pass a
     *                                                  ConnectorInterface instance.
     *
     * @return HttpClient
     */
    public static function buildHttpClient(
        LoopInterface $loop,
        $connector = null
    ) {
        // build a connector if none is given, or create one attaching given DnsResolver (old deprecated behavior)
        if (null === $connector || $connector instanceof DnsResolver) {
            $connector = static::buildConnector($loop, $connector);
        }

        if (!$connector instanceof ConnectorInterface) {
            throw new \InvalidArgumentException('$connector must be an instance of ConnectorInterface or DnsResolver');
        }

        return new HttpClient($loop, $connector);
    }
}
