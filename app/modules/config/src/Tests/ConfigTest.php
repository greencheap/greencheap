<?php

namespace GreenCheap\Config\Tests;

use GreenCheap\Config\Config;
use GreenCheap\Config\Loader\PhpLoader;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $values;

    public function setUp(): void
    {
        $values = [
            "foo" => [
                "bar" => "test",
            ],
        ];

        $this->config = new Config($values);
        $this->values = $values;
    }

    public function testHas()
    {
        $this->assertTrue($this->config->has("foo"));
        $this->assertTrue(!$this->config->has("none"));
    }

    public function testGet()
    {
        $this->assertEquals($this->config->get("foo.bar"), "test");
    }

    public function testToArray()
    {
        $this->assertEquals($this->config->toArray(), $this->values);
    }

    public function testSet()
    {
        $this->config->set("foo.bar2", "test2");
        $this->assertEquals($this->config->get("foo.bar2"), "test2");
        $this->config->offsetSet("foo.bar", "test3");
        $this->assertTrue($this->config->offsetExists("foo.bar"));
        $this->assertEquals($this->config->offsetGet("foo.bar"), "test3");
        $this->config->offsetUnset("foo.bar");
        $this->assertEquals($this->config->offsetGet("foo.bar"), null);
    }

    public function testDump()
    {
        $this->config->dump();
    }
}
