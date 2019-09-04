# Cache classes

### Simple use:
1. Cache::init('file'); // 'redis', 'memcached'
2. Cache::set($cache_id = string, $data = mixed, $ttl = int, $sub_dir = string)
3. Cache::get($cache_id = string, $sub_dir = string)
