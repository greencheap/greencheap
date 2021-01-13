<?php

namespace GreenCheap\Twig;

use Twig\Cache\FilesystemCache;

class TwigCache extends FilesystemCache
{
    protected string $dir;

    /**
     * {@inheritdoc}
     */
    public function __construct($directory, $options = 0)
    {
        $this->dir = $directory;

        parent::__construct($directory, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($name, $className)
    {
        return $this->dir.'/'.sha1($className).'.twig.cache';
    }
}
