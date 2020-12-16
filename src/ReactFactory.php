<?php

namespace Http\Adapter\React;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Http\Browser;
use React\Socket\ConnectorInterface;

/**
 * Factory wrapper for React instances.
 *
 * @author Stéphane Hulard <s.hulard@chstudio.fr>
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
     * Build a React Http Client.
     *
     * @param LoopInterface           $loop
     * @param ConnectorInterface|null $connector Only pass this argument if you need to customize DNS
     *                                           behaviour.
     *
     * @return Browser
     */
    public static function buildHttpClient(
        LoopInterface $loop,
        ConnectorInterface $connector = null
    ): Browser {
        return (new Browser($loop, $connector))
            ->withRejectErrorResponse(false)
            ->withFollowRedirects(false);
    }
}
