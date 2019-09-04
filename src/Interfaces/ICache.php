<?php

namespace WorkTestMax\Interfaces;

/**
 * Interface ICache
 * @package WorkTestMax\Interfaces
 */
interface ICache
{
    /**
     *
     * @param $cache_id
     * @param string $prefix
     *
     * @return string
     */
    public function get_path($cache_id, $prefix = 'cache');

    /**
     * @param $cache_id
     * @param string $prefix
     *
     * @return bool
     */
    public function exists($cache_id, $prefix = 'cache');

    /**
     *
     * @param $cache_id
     * @param string $prefix
     *
     * @return false
     */
    public function get($cache_id, $prefix = 'cache');

    /**
     *
     * @param $cache_id
     * @param null $data
     * @param int $ttl
     * @param null $prefix
     *
     *
     */
    public function set($cache_id, $data = null, $ttl = 3600, $prefix = null);

    public function clean($cache_id, $prefix = 'cache');
}