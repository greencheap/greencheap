<?php

namespace GreenCheap\Database\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType as BaseJsonArrayType;
use JetBrains\PhpStorm\Deprecated;

/**
 * Class JsonArrayType
 * @package GreenCheap\Database\Types
 * @deprecated since 3.1 use GreenCheap\Database\Types\JsonType
 */
#[Deprecated]
class JsonArrayType extends BaseJsonArrayType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return is_array($value) ? $value : parent::convertToPHPValue($value, $platform);
    }
}
