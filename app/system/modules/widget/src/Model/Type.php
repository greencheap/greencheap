<?php

namespace GreenCheap\Widget\Model;

use GreenCheap\Application as App;
use GreenCheap\Module\Module;

class Type extends Module implements TypeInterface
{
    const DEFAULT_TYPE_ICON = "system/theme:assets/images/default-widget.svg";

    /**
     * {@inheritdoc}
     */
    public function render(Widget $widget)
    {
        if (is_callable($this->get("render"))) {
            return call_user_func($this->get("render"), $widget);
        }
    }

    /**
     * @param string|null $icon
     * @return string
     */
    public function getIcon(string $icon = null): string
    {
        return App::url()->getStatic($icon) ?: App::url()->getStatic(self::DEFAULT_TYPE_ICON);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $type = $this->get(["name", "label", "icon"]);
        $type["icon"] = $this->getIcon(array_key_exists("icon", $type) ? $type["icon"] : self::DEFAULT_TYPE_ICON);
        return $type;
    }
}
