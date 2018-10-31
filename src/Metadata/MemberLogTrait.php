<?php

namespace Miaoxing\Member\Metadata;

/**
 * MemberLogTrait
 *
 * @property int $id
 * @property int $appId
 * @property int $cardId
 * @property int $userId
 * @property string $code 会员卡号
 * @property string $action 操作
 * @property string $description 操作说明
 * @property string $createdAt
 * @property int $createdBy
 */
trait MemberLogTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'app_id' => 'int',
        'card_id' => 'int',
        'user_id' => 'int',
        'code' => 'string',
        'action' => 'string',
        'description' => 'string',
        'created_at' => 'datetime',
        'created_by' => 'int',
    ];
}
