<?php

namespace WorkTestMax\Systems;

use Exception;
use WorkTestMax\Interfaces\ICache;

/**
 * Class CacheFile
 * @package WorkTestMax\Systems
 */
class CacheFile implements ICache
{
    /**
     * @var string
     */
    protected $cache_dir = '';

    /**
     * CacheFile constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/_TMP/cache_cache';
        if (file_exists($this->cache_dir) == false) {
            mkdir($this->cache_dir, 0777, true);
            if (file_exists($this->cache_dir) == false) {
                throw new Exception('Could not create directory for storing cache files. ' . $this->cache_dir);
            }
        }
    }

    /**
     * @param $cache_id
     * @param string $sub_dir
     * @return bool|string
     */
    public function get_path($cache_id, $sub_dir = 'cache')
    {
        $sub_dir = preg_replace('/\./', '', $sub_dir);
        $file = rtrim($this->cache_dir, '/') . '/' . trim($sub_dir, '/') . '/' . md5($cache_id) . '.php';
        $real_path = realpath($file);
        if ($real_path !== false) {
            $file = $real_path;
        }
        return $file;

    }

    /**
     * @param $cache_id
     * @param string $sub_dir
     * @return bool
     */
    public function exists($cache_id, $sub_dir = 'cache')
    {
        $file = $this->get_path($cache_id, $sub_dir);
        return is_file($file);
    }

    /**
     * @param $cache_id
     * @param string $sub_dir
     * @return array|bool|false|mixed|string
     */
    public function get($cache_id, $sub_dir = 'cache')
    {
        $file = $this->get_path($cache_id, $sub_dir);
        if (is_file($file) == true) {
            $content = file_get_contents($file);
            $content = explode("\n", $content);
            $expiry = intval($content[2]);
            $content = unserialize(stripcslashes($content[3]));

            // Если время кэширования не установлено, то есть кэш хранится бессрочно или кэш ещё актуальный.
            if ($expiry == 0 || $expiry > time()) {
                return $content;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * @param $cache_id
     * @param string $data
     * @param int $ttl
     * @param null $sub_dir
     * @return bool
     */
    public function set($cache_id, $data = '', $ttl = 3600, $sub_dir = null)
    {
        $expiry = time() + $ttl;
        $content = '<?phpexit;?>' . PHP_EOL;
        $content .= $cache_id . PHP_EOL;
        $content .= $expiry . PHP_EOL;
        $content .= addcslashes(serialize($data), "\x00..\x1F\x7F\x22\x27\x5C");
        $sub_dir = (string)$sub_dir;
        $sub_dir = trim($sub_dir, '/');
        if ($sub_dir == '/') {
            $sub_dir = '';
        }
        $dir = rtrim($this->cache_dir, '/') . '/' . $sub_dir;
        if (file_exists($dir) == false) {
            mkdir($dir, 0777, true);
            if (file_exists($dir) == false) {
                return false;
            }
        }
        $dir = rtrim($dir, '/');
        $file = $dir . '/' . md5($cache_id) . '.php';
        try {
            file_put_contents($file, $content);
        } catch (Exception $e) {
            return false;
        }
        if (is_file($file) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $cache_id
     * @param string $sub_dir
     */
    public function clean($cache_id, $sub_dir = 'cache')
    {
        $file = $this->get_path($cache_id, $sub_dir);
        if (is_file($file) == true) {
            unlink($file);
        }
    }
}

