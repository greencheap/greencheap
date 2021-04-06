<?php

namespace GreenCheap\Info\Controller;

use GreenCheap\Application as App;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access(admin=true)
 */
class InfoController
{
    /**
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$info' => "mixed"])]
    public function indexAction(): array
    {
        return [
            '$view' => [
                "title" => __("Info"),
                "name" => "system:modules/info/views/info.php",
            ],
            '$info' => App::info()->get(),
        ];
    }
}
