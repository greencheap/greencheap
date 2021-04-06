<?php

namespace GreenCheap\Site\Model;

use GreenCheap\Application as App;
use GreenCheap\Database\ORM\Annotation\Column;
use GreenCheap\Database\ORM\Annotation\Entity;
use GreenCheap\Database\ORM\Annotation\Id;
use GreenCheap\System\Model\DataModelTrait;
use GreenCheap\System\Model\NodeInterface;
use GreenCheap\System\Model\NodeTrait;
use GreenCheap\User\Model\AccessModelTrait;
use GreenCheap\User\Model\User;

/**
 * @Entity(tableClass="@system_node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessModelTrait, DataModelTrait, NodeModelTrait, NodeTrait;

    /**
     * @Column(type="integer")
     * @Id
     */
    public $id;

    /** @Column(type="integer") */
    public $parent_id = 0;

    /** @Column(type="integer") */
    public $priority = 0;

    /** @Column(type="integer") */
    public $status = 0;

    /** @Column(type="string") */
    public $slug;

    /** @Column(type="string") */
    public $path;

    /** @Column(type="string") */
    public $link;

    /** @Column(type="string") */
    public $title;

    /** @Column(type="string") */
    public $type;

    /** @Column(type="string") */
    public $menu = "";

    /** @var array */
    protected static array $properties = [
        "accessible" => "isAccessible",
    ];

    /**
     * Gets the node URL.
     *
     * @param  mixed  $referenceType
     * @return string
     */
    public function getUrl($referenceType = false): string
    {
        return App::url($this->link, [], $referenceType);
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function isAccessible(User $user = null): bool
    {
        return $this->status && $this->hasAccess($user ?: App::user());
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray(["url" => $this->getUrl("base")]);
    }
}
