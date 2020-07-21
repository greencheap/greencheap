<?php
namespace GreenCheap\System\Model;

use GreenCheap\Util\Arr;

trait StatusModelTrait
{
    /**
     * @var array
     */
    public static $statuses = [
        'STATUS_TRASH'          => 0,
        'STATUS_DRAFT'          => 1,
        'STATUS_UNPUBLISHED'    => 2,
        'STATUS_PUBLISHED'      => 3
    ];

    /**
     * @Column(type="integer")
     */
    public $status;

    /**
     * @return array
     */
    public static function getStatuses():array
    {
        return [
            self::getStatus('STATUS_TRASH') => __('Pending Review'),
            self::getStatus('STATUS_DRAFT') => __('Draft'),
            self::getStatus('STATUS_UNPUBLISHED') => __('Unpublished'),
            self::getStatus('STATUS_PUBLISHED') => __('Published')
        ];
    }

    /**
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    public static function getStatus($key, $default = null)
    {
        return Arr::get((array) self::$statuses, $key, $default);
    }

}
