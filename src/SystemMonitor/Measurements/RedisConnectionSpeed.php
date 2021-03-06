<?php

declare(strict_types=1);

namespace JildertMiedema\SystemMonitor\Measurements;

use Illuminate\Redis\RedisManager;
use JildertMiedema\SystemMonitor\System\MeasurementStore;

final class RedisConnectionSpeed implements Measurement
{
    /**
     * @var RedisManager
     */
    private $redis;

    public function __construct(RedisManager $redis)
    {
        $this->redis = $redis;
    }

    /**
     * The name of the.
     *
     * @return string
     */
    public function name(): string
    {
        return 'redis.speed';
    }

    /**
     * Runs the measurement.
     *
     * @param MeasurementStore $store
     * @param array            $data
     */
    public function run(MeasurementStore $store, array $data)
    {
        $connection = array_get($data, 'connection');
        $key = array_get($data, 'key');

        $database = $this->redis->connection($connection)->client();

        $timer_start = microtime(true);
        $database->ping('PING');
        $timer_end = microtime(true);
        $time = round(($timer_end - $timer_start) * 1000, 4);

        $store->storeTimer($key, $time . '|ms');
    }
}
