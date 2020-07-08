<?php

namespace GreenCheap\View\Helper;

use GreenCheap\View\View;

abstract class Helper implements HelperInterface
{
    /**
     * @var View
     */
    protected View $view;

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $this->view = $view;
    }
}
