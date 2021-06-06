<?php

namespace GreenCheap\Database\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType as BaseJsonType;

class JsonType extends BaseJsonType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return is_array($value) ? $value : parent::convertToPHPValue($value, $platform);
    }
}
