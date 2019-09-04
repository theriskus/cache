<?php

namespace WorkTestMax\Systems;

use Exception;
use Memcached;
use WorkTestMax\Interfaces\ICache;

class CacheMemcached implements ICache
{
    /**
     * @var bool
     */
    public static $enabled = false;
    /**
     * @var Memcached|null
     */
    public static $memcachedInstance = null;

    /**
     * CacheMemcached constructor.
     * @throws Exception
     */
    function __construct()
    {
        if (self::$memcachedInstance == null) {
            self::$memcachedInstance = new Memcached;
            try {
                self::$memcachedInstance->addServer('localhost',11121);
                self::$memcachedInstance->setOption(Memcached::OPT_SERIALIZER,0);
                self::$enabled = true;
            } catch (Exception $e) {
                throw new Exception('Memcached is not connected.');
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
        self::$memcachedInstance->get($prefix . ':' . $cache_id);
        return Memcached::RES_NOTFOUND !== self::$memcachedInstance->getResultCode();
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
        $val = self::$memcachedInstance->get($prefix . ':' . $cache_id);
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
            return self::$memcachedInstance->set($prefix . ':' . $cache_id, $content, $ttl);
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
        self::$memcachedInstance->delete($prefix . ':' . $cache_id);
        return true;
    }


}

?>