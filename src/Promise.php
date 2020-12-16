<?php

namespace Http\Adapter\React;

use Http\Client\Exception as HttplugException;
use Http\Promise\Promise as HttpPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use RuntimeException;
use UnexpectedValueException;

/**
 * React promise adapter implementation.
 *
 * @author Stéphane Hulard <s.hulard@chstudio.fr>
 *
 * @internal
 */
final class Promise implements HttpPromise
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
     * HTTP Request.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * Adapted ReactPHP promise.
     *
     * @var PromiseInterface
     */
    private $promise;

    /**
     * ReactPHP LoopInterface.
     *
     * @var LoopInterface
     */
    private $loop;

    public function __construct(PromiseInterface $promise, LoopInterface $loop, RequestInterface $request)
    {
        $this->state = self::PENDING;

        $this->request = $request;
        $this->loop = $loop;
        $this->promise = $promise->then(
            function (?ResponseInterface $response): ResponseInterface {
                $this->response = $response;
                $this->state = self::FULFILLED;

                return $response;
            },
            /**
             * @param mixed $reason
             */
            function ($reason): void {
                $this->state = self::REJECTED;

                if ($reason instanceof HttplugException) {
                    $this->exception = $reason;
                } elseif ($reason instanceof RuntimeException) {
                    $this->exception = new HttplugException\NetworkException($reason->getMessage(), $this->request, $reason);
                } elseif ($reason instanceof \Throwable) {
                    $this->exception = new HttplugException\TransferException('Invalid exception returned from ReactPHP', 0, $reason);
                } else {
                    $this->exception = new UnexpectedValueException('Reason returned from ReactPHP must be an Exception');
                }

                throw $this->exception;
            });
    }

    public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
    {
        return new self($this->promise->then($onFulfilled, $onRejected), $this->loop, $this->request);
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
        $loop = $this->loop;
        while (HttpPromise::PENDING === $this->getState()) {
            $loop->futureTick(function () use ($loop) {
                $loop->stop();
            });
            $loop->run();
        }

        if ($unwrap) {
            if (HttpPromise::REJECTED == $this->getState()) {
                throw $this->exception;
            }

            return $this->response;
        }
    }
}
