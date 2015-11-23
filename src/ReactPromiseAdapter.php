<?php

namespace Http\Adapter;

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface as ReactPromise;
use Http\Client\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * React promise adapter implementation
 * @author Stéphane Hulard <stephane@hlrd.me>
 */
class ReactPromiseAdapter implements Promise
{
    /**
     * Promise status
     * @var string
     */
    private $state = Promise::PENDING;

    /**
     * Adapted React promise
     * @var ReactPromise
     */
    private $promise;

    /**
     * PSR7 received response
     * @var ResponseInterface
     */
    private $response;

    /**
     * Execution error
     * @var Exception
     */
    private $exception;

    /**
     * React Event Loop used for synchronous processing
     * @var LoopInterface
     */
    private $loop;

    /**
     * Initialize the promise
     * @param ReactPromise $promise
     */
    public function __construct(ReactPromise $promise)
    {
        $promise->then(
            function(ResponseInterface $response) {
                $this->state = Promise::FULFILLED;
                $this->response = $response;
            },
            function(\Exception $error) {
                $this->state = Promise::REJECTED;
                $this->exception = $error;
            }
        );
        $this->promise = $promise;
    }

    /**
     * Allow to apply callable when the promise resolve
     * @param  callable|null $onFulfilled
     * @param  callable|null $onRejected
     * @return ReactPromiseAdapter
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $this->promise->then(function() use ($onFulfilled) {
            if( null !== $onFulfilled ) {
                call_user_func($onFulfilled, $this->response);
            }
        }, function() use ($onRejected) {
            if( null !== $onRejected ) {
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
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Set EventLoop used for synchronous processing
     * @param LoopInterface $loop
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
    public function wait()
    {
        if( null === $this->loop ) {
            throw new \LogicException("You must set the loop before wait!");
        }
        $this->loop->run();
    }
}
