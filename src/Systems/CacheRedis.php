<?php

namespace WorkTestMax\Systems;

use Exception;
use Redis;
use WorkTestMax\Interfaces\ICache;

class CacheRedis implements ICache
{
    /**
     * @var bool
     */
    public static $enabled = false;
    /**
     * @var Redis|null
     */
    public static $redisInstance = null;

    /**
     * CacheRedis constructor.
     */
    function __construct()
    {
        if (self::$redisInstance == null) {
            self::$redisInstance = new Redis();
            try {
                self::$redisInstance->pconnect('/var/run/redis/redis.sock'); // unix domain socket.
                self::$redisInstance->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
                self::$enabled = true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     *
     * @param $cache_id
     * @param string $prefix
     *
     * @return string
     */
    public function get_path($cache_id, $prefix = 'cache') {
        return '';
    }

    /**
     * @param $cache_id
     * @param string $prefix
     * @return bool
     */
    public function exists($cache_id, $prefix = 'cache')
    {
        $path_patterns = ['/^\/+|\/+$/', '/\/+/'];
        $patterns_replacers = ['', ':'];
        $cache_id = preg_replace($path_patterns, $patterns_replacers, $cache_id);
        $prefix = preg_replace($path_patterns, $patterns_replacers, $prefix);
        return boolval(self::$redisInstance->exists($prefix . ':' . $cache_id));
    }

    /**
     * @param $cache_id
     * @param string $prefix
     * @return array|bool|false|mixed
     */
    public function get($cache_id, $prefix = 'cache')
    {
        $path_patterns = ['/^\/+|\/+$/', '/\/+/'];
        $patterns_replacers = ['', ':'];
        $cache_id = preg_replace($path_patterns, $patterns_replacers, $cache_id);
        $prefix = preg_replace($path_patterns, $patterns_replacers, $prefix);
        $val = self::$redisInstance->get($prefix . ':' . $cache_id);
        if ($val == false) {
            return false;
        }
        $content = explode("\n", $val);
        $content = unserialize(stripcslashes($content[1]));
        return $content;
    }

    /**
     * @param $cache_id
     * @param string $data
     * @param int $ttl
     * @param string $prefix
     * @return bool
     */
    public function set($cache_id, $data = '', $ttl = 3600, $prefix = '')
    {
        $path_patterns = ['/^\/+|\/+$/', '/\/+/'];
        $patterns_replacers = ['', ':'];
        $cache_id = preg_replace($path_patterns, $patterns_replacers, $cache_id);
        $prefix = preg_replace($path_patterns, $patterns_replacers, $prefix);
        $content = time() . PHP_EOL;
        $content .= addcslashes(serialize($data), "\x00..\x1F\x7F\x22\x27\x5C");
        try {
            return self::$redisInstance->set($prefix . ':' . $cache_id, $content, $ttl);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $cache_id
     * @param string $prefix
     * @return bool
     */
    public function clean($cache_id, $prefix = 'cache')
    {
        $path_patterns = ['/^\/+|\/+$/', '/\/+/'];
        $patterns_replacers = ['', ':'];
        $cache_id = preg_replace($path_patterns, $patterns_replacers, $cache_id);
        $prefix = preg_replace($path_patterns, $patterns_replacers, $prefix);
        self::$redisInstance->unlink($prefix . ':' . $cache_id);
        return true;
    }

    /**
     * @param $prefix
     * @return int
     */
    public function clean_path($prefix)
    {
        $res = self::$redisInstance->keys($prefix . '*');
        if (count($res) > 0) {
            self::$redisInstance->unlink($res);
        }
        return count($res);
    }

}

?>