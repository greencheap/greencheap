<?php

namespace GreenCheap\Filter;

/**
 * This filter converts the value to boolean.
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return (bool) @strval($value);
    }
}
