<?php

namespace Http\Adapter\React;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Dns\Resolver\Resolver as DnsResolver;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\HttpClient\Client as HttpClient;
use React\HttpClient\Factory as HttpClientFactory;
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
        return null !== $dns
            ? new Connector($loop, ['dns' => $dns])
            : new Connector($loop);
    }

    /**
     * Build a React Http Client.
     *
     * @param LoopInterface                       $loop
     * @param ConnectorInterface|DnsResolver|null $connector Passing a DnsResolver instance is deprecated. Should pass a
     *                                                       ConnectorInterface instance.
     *
     * @return HttpClient
     */
    public static function buildHttpClient(
        LoopInterface $loop,
        $connector = null
    ) {
        if (class_exists(HttpClientFactory::class)) {
            // if HttpClientFactory class exists, use old behavior for backwards compatibility
            return static::buildHttpClient04($loop, $connector);
        } else {
            return static::buildHttpClient05($loop, $connector);
        }
    }

    /**
     * Builds a React Http client v0.4 style
     *
     * @param LoopInterface    $loop
     * @param DnsResolver|null $dns
     *
     * @return HttpClient
     */
    private static function buildHttpClient04(
        LoopInterface $loop,
        $dns = null
    ) {
        // create dns resolver if one isn't provided
        if (null === $dns) {
            $dns = static::buildDnsResolver($loop);
        }

        // validate connector instance for proper error reporting
        if (!$dns instanceof DnsResolver) {
            throw new \InvalidArgumentException('$connector must be an instance of DnsResolver');
        }

        $factory = new HttpClientFactory();

        return $factory->create($loop, $dns);
    }

    /**
     * Builds a React Http client v0.5 style
     *
     * @param LoopInterface                       $loop
     * @param DnsResolver|ConnectorInterface|null $connector
     *
     * @return HttpClient
     */
    private static function buildHttpClient05(
        LoopInterface $loop,
        $connector = null
    ) {
        // build a connector with given DnsResolver if provided (old deprecated behavior)
        if ($connector instanceof DnsResolver) {
            $connector = static::buildConnector($loop, $connector);
        }

        // validate connector instance for proper error reporting
        if (null !== $connector && !$connector instanceof ConnectorInterface) {
            throw new \InvalidArgumentException(
                '$connector must be an instance of DnsResolver or ConnectorInterface'
            );
        }

        return new HttpClient($loop, $connector);
    }
}
