<?php

namespace GreenCheap\Filter;

/**
 * This filter converts the value to float.
 */
class FloatFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return floatval((string) $value);
    }
}
