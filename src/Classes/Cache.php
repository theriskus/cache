<?php

namespace WorkTestMax\Classes;

use Exception;
use WorkTestMax\Systems\CacheFile;
use WorkTestMax\Systems\CacheRedis;
use WorkTestMax\Systems\CacheMemcached;

/**
 * Class Cache
 * @package WorkTestMax
 */
class Cache
{
    /**
     * @var bool
     */
    static public $debug_mode = false;

    // Для отладки.
    /**
     * @var array
     */
    static protected $debug_data = [
        'recorded_keys' => [],
        'read_keys' => []
    ];

    /**
     * @var bool
     */
    static protected $initialized = false;

    /**
     * @var null
     */
    static protected $engine = null;

    /**
     * @param string $engine_type
     * @return bool
     * @throws Exception
     */
    static public function init($engine_type = 'file')
    {
        if (self::$initialized == false) {
            switch ($engine_type) {
                case 'redis':
                    self::$engine = new CacheRedis();
                    break;
                case 'memcached':
                    self::$engine = new CacheMemcached();
                    break;
                case 'file':
                    self::$engine = new CacheFile();
                    break;
                default:
                    throw new Exception('This engine is not supported.');
                    break;
            }
            self::$initialized = true;
        }
        return self::$initialized;
    }

    static public function getEngine()
    {
        return self::$engine;
    }

    /**
     * @throws Exception
     */
    static protected function check_initialization()
    {
        if (self::$initialized == false) {
            throw new Exception('The caching system is not initialized.');
        }
    }

    /**
     *
     * @param string $cache_id
     * @param string $sub_dir
     * @throws Exception
     *
     * @return mixed
     */
    static public function get_path($cache_id, $sub_dir = 'cache')
    {
        self::check_initialization();
        return self::$engine->get_path($cache_id, $sub_dir);
    }

    /**
     *
     * @param string $cache_id
     * @param mixed $data
     * @param int $ttl
     * @param string $sub_dir
     * @throws Exception
     *
     * @return bool
     */
    static public function set($cache_id, $data, $ttl = 3600, $sub_dir = 'cache')
    {
        self::check_initialization();
        if (self::$debug_mode == true) {
            if (array_key_exists($cache_id, self::$debug_data['recorded_keys']) == false) {
                self::$debug_data['recorded_keys'][$cache_id] = 0;
            }
            self::$debug_data['recorded_keys'][$cache_id]++;
        }
        return self::$engine->set($cache_id, $data, $ttl, $sub_dir);
    }


    /**
     *
     * @param string $cache_id
     * @param string $sub_dir
     * @throws Exception
     *
     * @return bool
     */
    static public function exists($cache_id, $sub_dir = 'cache')
    {
        self::check_initialization();
        return self::$engine->exists($cache_id, $sub_dir);
    }


    /**
     *
     * @param string $cache_id
     * @param string $sub_dir
     * @throws Exception
     * @return mixed
     */
    static public function get($cache_id, $sub_dir = 'cache')
    {
        self::check_initialization();
        if (self::$debug_mode == true) {
            if (array_key_exists($cache_id, self::$debug_data['read_keys']) == false) {
                self::$debug_data['read_keys'][$cache_id] = 0;
            }
            self::$debug_data['read_keys'][$cache_id]++;
        }
        return self::$engine->get($cache_id, $sub_dir);
    }

    /**
     *
     * @param string $cache_id
     * @param string $sub_dir
     * @throws Exception
     *
     * @return boolean
     */
    static public function clean($cache_id, $sub_dir = 'cache')
    {
        self::check_initialization();
        return self::$engine->clean($cache_id, $sub_dir);
    }

    /**
     *
     * @return array
     */
    static public function get_debug_info()
    {
        return self::$debug_data;
    }

}