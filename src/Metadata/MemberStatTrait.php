<?php

namespace Miaoxing\Member\Metadata;

/**
 * MemberStatTrait
 *
 * @property int $id
 * @property int $appId
 * @property int $cardId
 * @property string $statDate
 * @property int $receiveUser
 * @property int $receiveCount
 * @property int $firstConsumeUser
 * @property int $totalReceiveUser
 * @property int $totalReceiveCount
 * @property int $totalFirstConsumeUser
 * @property string $createdAt
 * @property string $updatedAt
 */
trait MemberStatTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'app_id' => 'int',
        'card_id' => 'int',
        'stat_date' => 'date',
        'receive_user' => 'int',
        'receive_count' => 'int',
        'first_consume_user' => 'int',
        'total_receive_user' => 'int',
        'total_receive_count' => 'int',
        'total_first_consume_user' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
