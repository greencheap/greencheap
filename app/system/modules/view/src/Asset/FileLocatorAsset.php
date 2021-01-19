<?php

namespace GreenCheap\View\Asset;

use GreenCheap\Application as App;

class FileLocatorAsset extends FileAsset
{
    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        if (!($path = $this->getPath())) {
            return parent::getSource();
        }

        $path = App::file()->getUrl($path);

        if ($version = $this->getOption('version')) {
            $path .= (!str_contains($path, '?') ? '?' : '&') . 'v=' . $version;
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return App::locator()->get($this->source) ?: false;
    }
}
