<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Score\Service\ScoreLog;
use Miaoxing\Score\Service\ScoreLogRecord;
use Miaoxing\WechatCard\Service\UserWechatCardRecord;
use Miaoxing\WechatCard\Service\WechatCardRecord;

/**
 * @property MemberLevelRecord $memberLevel
 * @property WechatCardRecord $wechatCard
 * @property User $user
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
        return $this->hasOne('memberLevel', 'id', 'level_id');
    }

    public function wechatCard()
    {
        return $this->hasOne('wechatCard', 'id', 'card_id');
    }

    public function getImage()
    {
        return $this->memberLevel['image'] ?: $this->wechatCard['background_pic_url'];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this['field_list'] = (array) json_decode($this['field_list'], true);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['field_list'] = json_encode($this['field_list']);
    }

    public function afterSave()
    {
        parent::afterSave();
        $this['field_list'] = (array) json_decode($this['field_list'], true);
    }

    /**
     * 更新会员卡的积分,卡券数据
     */
    public function syncCountData()
    {
        $user = $this->user;

        $score = wei()->score->getScore($user);
        $usedScore = wei()->scoreLog()
            ->curApp()
            ->select('SUM(score)')
            ->fetchColumn([
                'user_id' => $user['id'],
                'action' => ScoreLogRecord::ACTION_SUB,
            ]);

        $totalCardCount = wei()->userWechatCard()->curApp()->count(['user_id' => $user['id']]);
        $cardCount = wei()->userWechatCard()->curApp()->count([
            'user_id' => $user['id'],
            'status' => UserWechatCardRecord::STATUS_NORMAL,
        ]);

        $this->save([
            'card_count' => $cardCount,
            'total_card_count' => $totalCardCount,
            'used_card_count' => $totalCardCount - $cardCount,
            'score' => $score,
            'used_score' => $usedScore,
            'total_score' => $score + $usedScore,
        ]);
    }
}
