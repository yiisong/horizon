<?php

namespace Laravel\Horizon;

use Illuminate\Contracts\Redis\Factory as RedisFactory;

class Lock
{
    /**
     * The Redis factory implementation.
     *
     * @var RedisFactory
     */
    public $redis;

    /**
     * Create a Horizon lock manager.
     *
     * @param  RedisFactory  $redis
     * @return void
     */
    public function __construct(RedisFactory $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Execute the given callback if a lock can be aquired.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @param  int  $seconds
     * @return void
     */
    public function with($key, $callback, $seconds = 60)
    {
        if ($this->get($key, $seconds)) {
            try {
                call_user_func($callback);
            } finally {
                $this->release($key);
            }
        }
    }

    /**
     * Determine if a lock exists for the given key.
     *
     * @param  string  $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->connection()->exists($key) === 1;
    }

    /**
     * Attempt to get a lock for the given key.
     *
     * @param  string  $key
     * @param  int  $seconds
     * @return bool
     */
    public function get($key, $seconds = 60)
    {
        $result = $this->connection()->setnx($key, 1);

        if ($result === 1) {
            $this->connection()->expire($key, $seconds);
        }

        return $result === 1;
    }

    /**
     * Release the lock for the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function release($key)
    {
        $this->connection()->del($key);
    }

    /**
     * Get the Redis connection instance.
     *
     * @return \Illuminate\Redis\Connetions\Connection
     */
    public function connection()
    {
        return $this->redis->connection('horizon-locks');
    }
}
