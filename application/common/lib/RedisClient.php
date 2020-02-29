<?php

namespace app\common\lib;

use Redis;
use Yii;

class RedisClient
{

    private static $instances = [];
    private $redis;

    /**
     * @param string $name
     * @return RedisClient
     */
    public static function getInstance()
    {
        if (empty(self::$instances)) {
            self::$instances = new self();
        }
        return self::$instances;
    }

    private function __construct()
    {
        $this->redis = new Redis();
        $result = $this->redis->connect(config('redis.host'), config('redis.port'), config('redis.timeout'));
        if (!$result) {
            throw new \Exception('redis connect error');
        }
    }

    public function __call($name, $arguments)
    {
        echo $name . PHP_EOL;
        print_r($arguments);
    }

    public function get($key)
    {
        if (!$key) return '';
        return $this->redis->get($key);
    }

    public function set($key, $value, $time = 0)
    {
        if (!$key) return '';
        $value = is_array($value) ? json_encode($value) : $value;
        if (!$time) return $this->redis->set($key, $value);
        return $this->redis->set($key, $value, $time);
    }

    public function lpush($key, $value)
    {
        return $this->redis->lpush($key, $value);
    }

    public function rpop($key)
    {
        return $this->redis->rpop($key);
    }

    public function llen($key)
    {
        return $this->redis->llen($key);
    }

    public function incr($key)
    {
        return $this->redis->incr($key);
    }

    public function decr($key)
    {
        return $this->redis->decr($key);
    }

    public function expire($key, $ttl)
    {
        return $this->redis->expire($key, $ttl);
    }

    public function del($key)
    {
        return $this->redis->del($key);
    }

    public function hset($key, $field, $value)
    {
        return $this->redis->hSet($key, $field, $value);
    }

    public function hLen($key)
    {
        return $this->redis->hLen($key);
    }

    public function hget($key, $field)
    {
        return $this->redis->hGet($key, $field);
    }

    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    public function hdel($key, $field)
    {
        return $this->redis->hDel($key, $field);
    }

    public function setnx($key, $value)
    {
        return $this->redis->setnx($key, $value);
    }

    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    public function brPop($key, $timeout)
    {
        return $this->redis->brPop($key, $timeout);
    }

    public function lRange($key, $start, $end)
    {
        return $this->redis->lRange($key, $start, $end);
    }

    public function sAdd($key, $value)
    {
        return $this->redis->sAdd($key, $value);
    }

    public function sMembers($key)
    {
        return $this->redis->sMembers($key);
    }

    public function sRem($key, $value)
    {
        return $this->redis->sRem($key, $value);
    }

    public function zadd($key, $weight, $value)
    {
        return $this->redis->zAdd($key, $weight, $value);
    }

    public function zrange($key, $start, $end)
    {
        return $this->redis->zRange($key, $start, $end);
    }

    public function keys($key)
    {
        return $this->redis->keys($key);
    }

    public function zscore($key, $member)
    {
        return $this->redis->zScore($key, $member);
    }

    public function zRem($key, $member)
    {
        return $this->redis->zRem($key, $member);
    }

    public function zRevRange($key, $start, $end, $withscores = null)
    {
        return $this->redis->zRevRange($key, $start, $end, $withscores);
    }

    public function rPush($key, $member)
    {
        return $this->redis->rPush($key, $member);
    }

    /**
     * 修剪(trim)一个已存在的 list，这样 list 就会只包含指定范围的指定元素
     *
     * @param $key
     * @param $start
     * @param $stop
     * @return array
     */
    public function lTrim($key, $start, $stop)
    {
        return $this->redis->lTrim($key, $start, $stop);
    }

    public function hExists($key, $field)
    {
        return $this->redis->hExists($key, $field);
    }
}
