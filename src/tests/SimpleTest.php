<?php
use WorkTestMax\Classes\Cache;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleTest
 */
class SimpleTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCacheSetGet()
    {
        Cache::init('file');
        $data = 'hello';
        $id = 'test_id';
        $this->expectOutputString($data);
        Cache::set($id, $data, 3600, 'cache');
        $get = Cache::get($id, 'cache');
        print $get;
    }

    /**
     * @throws Exception
     */
    public function testInit()
    {
        $this->expectExceptionMessage('This engine is not supported.');
        Cache::init('other_engine');
    }

    /**
     * @throws Exception
     */
    public function testNotExistsId()
    {
        Cache::init('file');
        $this->assertFalse(Cache::get('not_exists', 'cache'));
    }

    /**
     * @throws Exception
     */
    public function testTtlString()
    {
        $this->expectException(Exception::class);
        Cache::init('file');
        Cache::set('test', 'this', 'exception', 'error');
    }

}
