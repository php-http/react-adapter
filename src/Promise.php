<?php

namespace Http\Adapter\React;

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface as ReactPromise;
use Http\Client\Exception;
use Http\Promise\Promise as HttpPromise;
use Psr\Http\Message\ResponseInterface;

/**
 * React promise adapter implementation.
 *
 * @author Stéphane Hulard <stephane@hlrd.me>
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
     * Adapted React promise.
     *
     * @var ReactPromise
     */
    private $promise;

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
     * React Event Loop used for synchronous processing.
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     * Initialize the promise.
     *
     * @param ReactPromise $promise
     */
    public function __construct(ReactPromise $promise)
    {
        $promise->then(
            function (ResponseInterface $response) {
                $this->state = HttpPromise::FULFILLED;
                $this->response = $response;
            },
            function (Exception $error) {
                $this->state = HttpPromise::REJECTED;
                $this->exception = $error;
            }
        );
        $this->promise = $promise;
    }

    /**
     * Allow to apply callable when the promise resolve.
     *
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     *
     * @return ReactPromiseAdapter
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $this->promise->then(function () use ($onFulfilled) {
            if (null !== $onFulfilled) {
                call_user_func($onFulfilled, $this->response);
            }
        }, function () use ($onRejected) {
            if (null !== $onRejected) {
                call_user_func($onRejected, $this->exception);
            }
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set EventLoop used for synchronous processing.
     *
     * @param LoopInterface $loop
     *
     * @return ReactPromiseAdapter
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = true)
    {
        if (null === $this->loop) {
            throw new \LogicException('You must set the loop before wait!');
        }
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
