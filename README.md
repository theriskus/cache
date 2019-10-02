# Simple cache

#### Requirements
1. PHP 5.6 => above
2. [Composer](https://getcomposer.org/download)
2. Redis 5.0 (optional)
3. Memcached 1.5.X (optional)

### Installing
To install this module, you're should use composer library :
``` composer require theriskus/cache``` 

### Simple use:
1. For initialize add to your bootstrap file this: ``` Cache::init(driver);``` and add ```use WorkTestMax\Classes\Cache```
##### __Driver must be string and equal: 'redis' or 'memcached' or 'file'__
2. For set any params: ``` Cache::set(string $cache_id, mixed $data, int $ttl, string $sub_dir = '')```
3. For get any params: ``` Cache::get(string $cache_id, string $sub_dir = '');```
