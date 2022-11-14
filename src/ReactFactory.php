<?php

namespace Http\Adapter\React;

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
     * Build a React Http Client.
     *
     * @param ConnectorInterface|null $connector only pass this argument if you need to customize DNS
     *                                           behaviour
     */
    public static function buildHttpClient(
        ConnectorInterface $connector = null
    ): Browser {
        return (new Browser($connector))
            ->withRejectErrorResponse(false)
            ->withFollowRedirects(false);
    }
}
