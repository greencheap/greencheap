<?php

namespace GreenCheap\Site\Model;

use GreenCheap\Database\ORM\Annotation\Column;
use GreenCheap\Database\ORM\Annotation\Entity;
use GreenCheap\Database\ORM\Annotation\Id;
use GreenCheap\Database\ORM\ModelTrait;
use GreenCheap\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@system_page")
 */
class Page implements \JsonSerializable
{
    use DataModelTrait, ModelTrait;

    /**
     * @Column(type="integer")
     * @Id
     */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column */
    public $content = "";
}
