<?php

namespace GreenCheap\Site\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\Site\Model\Page;
use GreenCheap\User\Annotation\Access;

/**
 * @Access("site: manage site")
 */
class PageApiController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction(): array
    {
        return array_values(Page::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return Page
     */
    public function getAction($id): Page
    {
        return Page::find($id);
    }
}
