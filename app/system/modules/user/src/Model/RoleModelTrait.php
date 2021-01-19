<?php

namespace GreenCheap\User\Model;

use Doctrine\DBAL\Exception;
use GreenCheap\Database\ORM\Annotation\Saving;
use GreenCheap\Database\ORM\ModelTrait;

trait RoleModelTrait
{
    use ModelTrait;

    /**
     * @Saving
     * @param $event
     * @param Role $role
     * @throws Exception
     */
    public static function saving($event, Role $role)
    {
        if (!$role->id) {
            $role->priority = self::getConnection()->fetchOne('SELECT MAX(priority) + 1 FROM @system_role');
        }
    }
}
