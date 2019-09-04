<?php

namespace WorkTestMax\Interfaces;

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
     * Особенность работы: Если кэшируется булево значение, которое равно false, тогда функция всегда будет возвращать false.
     *
     * @param $cache_id
     * @param string $prefix
     *
     * @return false - вышел срок или нет такого ключа.
     */
    public function get($cache_id, $prefix = 'cache');

    /**
     * кэшировать относительно текущего timestamp.
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