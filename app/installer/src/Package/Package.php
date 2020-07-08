<?php

namespace GreenCheap\Installer\Package;

use GreenCheap\Util\Arr;

class Package implements PackageInterface
{
    /**
     * @var array
     */
    protected array $data;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->data, $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
