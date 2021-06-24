<?php

namespace GreenCheap\System\Model;

use GreenCheap\Database\ORM\Annotation\Column;
use GreenCheap\Util\Arr;
use JetBrains\PhpStorm\Pure;

/**
 * Trait DataModelTrait
 * @package GreenCheap\System\Model
 */
trait DataModelTrait
{
    /** @Column(type="json") */
    public $data;

    /**
     * Gets a data value.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    #[Pure]
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get((array) $this->data, $key, $default);
    }

    /**
     * Sets a data value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, mixed $value)
    {
        if (null === $this->data) {
            $this->data = [];
        }

        Arr::set($this->data, $key, $value);
    }
}
