<?php

namespace GreenCheap\Filter\Tests;

use GreenCheap\Filter\SlugifyFilter;

class SlugifyTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new SlugifyFilter();

        $values = [
            "PAGEKIT" => "greencheap",
            ":#*\"@+=;!><&.%()/'\\|[]" => "",
            "  a b ! c   " => "a-b-c",
        ];

        foreach ($values as $in => $out) {
            $this->assertEquals($out, $filter->filter($in));
        }
    }
}
