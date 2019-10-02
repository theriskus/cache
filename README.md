# Simple cache

#### Requirements
1. PHP 5.6 => above
2. Redis 5.0 (optional)
3. Memcached 1.5.X (optional)

### Simple use:
1. For initialize add to your bootstrap file this: ```php Cache::init(driver);```
##### __Driver must be string and equal: 'redis' or 'memcached' or 'file'__
2. For set any params: ```php Cache::set(string $cache_id, mixed $data, int $ttl, string $sub_dir = '')```
3. For get any params: ```php Cache::get(string $cache_id, string $sub_dir = '');```
