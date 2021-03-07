<?php

    namespace GuzzleHttp\Promise;

    /**
     * A promise that has been fulfilled.
     *
     * Thenning off of this promise will invoke the onFulfilled callback
     * immediately and ignore other callbacks.
     */
    class FulfilledPromise implements PromiseInterface
    {
        private $value;

        public function __construct( $value )
        {
            if ( method_exists($value, 'then') ) {
                throw new \InvalidArgumentException(
                    'You cannot create a FulfilledPromise with a promise.'
                );
            }

            $this->value = $value;
        }

        public function cancel()
        {
            // pass
        }

        public function getState()
        {
            return self::FULFILLED;
        }

        public function otherwise( callable $onRejected )
        {
            return $this->then(NULL, $onRejected);
        }

        public function then(
            callable $onFulfilled = NULL,
            callable $onRejected = NULL
        ) {
            // Return itself if there is no onFulfilled function.
            if ( !$onFulfilled ) {
                return $this;
            }

            $queue = queue();
            $p     = new Promise([$queue, 'run']);
            $value = $this->value;
            $queue->add(
                static function() use ( $p, $value, $onFulfilled ) {
                    if ( $p->getState() === self::PENDING ) {
                        try {
                            $p->resolve($onFulfilled($value));
                        } catch ( \Throwable $e ) {
                            $p->reject($e);
                        } catch ( \Exception $e ) {
                            $p->reject($e);
                        }
                    }
                }
            );

            return $p;
        }

        public function reject( $reason )
        {
            throw new \LogicException("Cannot reject a fulfilled promise");
        }

        public function resolve( $value )
        {
            if ( $value !== $this->value ) {
                throw new \LogicException("Cannot resolve a fulfilled promise");
            }
        }

        public function wait( $unwrap = TRUE, $defaultDelivery = NULL )
        {
            return $unwrap ? $this->value : NULL;
        }
    }
