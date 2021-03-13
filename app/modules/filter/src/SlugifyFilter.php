<?php

namespace GreenCheap\Filter;

/**
 * This filter converts the value unicode slug.
 */
class SlugifyFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $value = preg_replace('/\xE3\x80\x80/', ' ', (string) $value);
        $value = str_replace('-', ' ', $value);
        $value = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $value);
        $value = str_replace('?', '', $value);
        $value = trim(mb_strtolower($value, 'UTF-8'));
        $value = str_replace($this->getLatinExtend(), $this->getEnglishLetters(), $value);
        $value = preg_replace('#\x20+#', '-', $value);

        return $value;
    }

    /**
     * @return array
     */
    protected function getLatinExtend():array
    {
        return ["ı", "ğ", "ü", "ş", "ö", "ç"];
    }

    /**
     * @return array
     */
    protected function getEnglishLetters():array
    {
        return ["i", "g", "u", "s", "o", "c"];
    }
}
