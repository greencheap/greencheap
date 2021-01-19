<?php

namespace GreenCheap\Filter\Tests;

use GreenCheap\Filter\AddRelNofollowFilter;

class AddRelNofollowTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new AddRelNofollowFilter;

        $this->assertTrue(str_contains($filter->filter('<a href="http://www.example.com/">text</a>'), 'rel="nofollow"'));
        $this->assertTrue(str_contains($filter->filter('<A href="http://www.example.com/">text</a>'), 'rel="nofollow"'));

        // TODO: these tests should validate too
//        $this->assertTrue(false !== strpos($filter->filter('<a/href=\"http://www.example.com/\">text</a>'), 'rel="nofollow"'));
//        $this->assertTrue(false !== strpos($filter->filter('<\0a\0 href=\"http://www.example.com/\">text</a>'), 'rel="nofollow"'));
//        $this->assertFalse(strpos($filter->filter('<a href="http://www.example.com/" rel="follow">text</a>'), 'rel="follow"'));
    }

}
