<?php

namespace Miaoxing\Member\Metadata;

/**
 * MemberTrait
 *
 * @property int $id
 * @property int $appId
 * @property int $cardId 微信卡券编号
 * @property string $cardWechatId 微信卡券的微信编号
 * @property string $code 会员卡号
 * @property string $outCode 外部卡号
 * @property int $userId
 * @property string $wechatOpenId
 * @property int $levelId 等级
 * @property bool $isSpecifiedLevel 是否指定了等级
 * @property float $balance
 * @property bool $status 当前用户会员卡状态
 * @property string $fieldList 开发者设置的会员卡会员信息类目，如等级
 * @property string $consumedAt 首次消费时间
 * @property string $lastConsumedAt
 * @property int $cardCount 现有的优惠券数
 * @property int $totalCardCount 领取的优惠券数
 * @property int $usedCardCount 使用的优惠券数
 * @property int $score
 * @property int $usedScore 使用过的积分数
 * @property int $totalScore 总的积分数
 * @property bool $isGiveByFriend 是否为转赠领取，1代表是，0代表否
 * @property string $friendUserName
 * @property string $outerStr 领取场景值，用于领取渠道数据统计
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property string $deletedAt
 * @property int $deletedBy
 */
trait MemberTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'app_id' => 'int',
        'card_id' => 'int',
        'card_wechat_id' => 'string',
        'code' => 'string',
        'out_code' => 'string',
        'user_id' => 'int',
        'wechat_open_id' => 'string',
        'level_id' => 'int',
        'is_specified_level' => 'bool',
        'balance' => 'float',
        'status' => 'bool',
        'field_list' => 'string',
        'consumed_at' => 'datetime',
        'last_consumed_at' => 'datetime',
        'card_count' => 'int',
        'total_card_count' => 'int',
        'used_card_count' => 'int',
        'score' => 'int',
        'used_score' => 'int',
        'total_score' => 'int',
        'is_give_by_friend' => 'bool',
        'friend_user_name' => 'string',
        'outer_str' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_at' => 'datetime',
        'deleted_by' => 'int',
    ];
}
