<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseModel;
use Miaoxing\WechatCard\Service\WechatCardRecord;

/**
 * @property MemberLevelRecord $memberLevel
 * @property WechatCardRecord $wechatCard
 */
class MemberRecord extends BaseModel
{
    protected $table = 'members';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createAtColumn = 'created_at';

    protected $updateAtColumn = 'updated_at';

    protected $createdByColumn = 'created_by';

    protected $updatedByColumn = 'updated_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';

    protected $userIdColumn = 'user_id';

    public function user()
    {
        return $this->belongsTo('user');
    }

    public function memberLevel()
    {
        return $this->hasOne('memberLevel', 'id', 'level');
    }

    public function wechatCard()
    {
        return $this->hasOne('wechatCard', 'wechat_id', 'wechat_card_id');
    }

    public function getImage()
    {
        return $this->memberLevel['image'] ?: $this->wechatCard['background_pic_url'];
    }
}
