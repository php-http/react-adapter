<?php

namespace Http\Adapter\React;

use React\EventLoop\LoopInterface;
use Http\Client\Exception;
use Http\Promise\Promise as HttpPromise;
use Psr\Http\Message\ResponseInterface;

/**
 * React promise adapter implementation.
 *
 * @author Stéphane Hulard <stephane@hlrd.me>
 *
 * @internal
 */
class Promise implements HttpPromise
{
    /**
     * Promise status.
     *
     * @var string
     */
    private $state = HttpPromise::PENDING;

    /**
     * PSR7 received response.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Execution error.
     *
     * @var Exception
     */
    private $exception;

    /**
     * @var callable|null
     */
    private $onFulfilled;

    /**
     * @var callable|null
     */
    private $onRejected;

    /**
     * React Event Loop used for synchronous processing.
     *
     * @var LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * Allow to apply callable when the promise resolve.
     *
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     *
     * @return Promise
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $newPromise = new Promise($this->loop);

        $onFulfilled = $onFulfilled !== null ? $onFulfilled : function (ResponseInterface $response) {
            return $response;
        };

        $onRejected = $onRejected !== null ? $onRejected : function (Exception $exception) {
            throw $exception;
        };

        $this->onFulfilled = function(ResponseInterface $response) use ($onFulfilled, $newPromise) {
            try {
                $newPromise->resolve($onFulfilled($response));
            } catch (Exception $exception) {
                $newPromise->reject($exception);
            }
        };

        $this->onRejected = function(Exception $exception) use ($onRejected, $newPromise) {
            try {
                $newPromise->resolve($onRejected($exception));
            } catch (Exception $exception) {
                $newPromise->reject($exception);
            }
        };

        return $newPromise;
    }

    /**
     * Resolve this promise
     *
     * @param ResponseInterface $response
     *
     * @internal
     */
    public function resolve(ResponseInterface $response)
    {
        if ($this->state !== HttpPromise::PENDING) {
            throw new \RuntimeException('Promise is already resolved');
        }

        $this->state = HttpPromise::FULFILLED;
        $this->response = $response;
        $onFulfilled = $this->onFulfilled;

        if (null !== $onFulfilled) {
            $onFulfilled($response);
        }
    }

    /**
     * Reject this promise
     *
     * @param Exception $exception
     *
     * @internal
     */
    public function reject(Exception $exception)
    {
        if ($this->state !== HttpPromise::PENDING) {
            throw new \RuntimeException('Promise is already resolved');
        }

        $this->state = HttpPromise::REJECTED;
        $this->exception = $exception;
        $onRejected = $this->onRejected;

        if (null !== $onRejected) {
            $onRejected($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = true)
    {
        while (HttpPromise::PENDING === $this->getState()) {
            $this->loop->tick();
        }

        if ($unwrap) {
            if (HttpPromise::REJECTED == $this->getState()) {
                throw $this->exception;
            }

            return $this->response;
        }
    }
}
