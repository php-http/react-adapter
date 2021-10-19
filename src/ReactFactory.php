<?php

namespace Http\Adapter\React;

use \React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
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
     */
    public static function buildEventLoop(): LoopInterface
    {
        return Loop::get();
    }

    /**
     * Build a React Http Client.
     *
     * @param ConnectorInterface|null $connector only pass this argument if you need to customize DNS
     *                                           behaviour
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
