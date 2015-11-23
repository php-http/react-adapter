<?php

namespace Http\Adapter;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Dns\Resolver\Resolver as DnsResolver;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\HttpClient\Factory as HttpClientFactory;
use React\HttpClient\Client as HttpClient;

/**
 * Factory wrapper for React instances
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactFactory
{
    /**
     * Build a react Event Loop
     * @return LoopInterface
     */
    public static function buildEventLoop()
    {
        return EventLoopFactory::create();
    }

    /**
     * Build a React Dns Resolver
     * @param  LoopInterface $loop
     * @param  string        $dns
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
     * Build a React Http Client
     * @param  LoopInterface $loop
     * @param  Resolver      $dns
     * @return HttpClient
     */
    public static function buildHttpClient(
        LoopInterface $loop,
        DnsResolver $dns = null
    ) {
        if( null === $dns ) {
            $dns = self::buildDnsResolver($loop);
        }

        $factory = new HttpClientFactory();
        return $factory->create($loop, $dns);
    }
}
