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
        $this->loadCardCount();

        $user = $this->user;
        $score = wei()->score->getScore($user);
        $usedScore = wei()->scoreLog()
            ->curApp()
            ->select('SUM(score)')
            ->fetchColumn([
                'user_id' => $user['id'],
                'action' => ScoreLogRecord::ACTION_SUB,
            ]);

        // 对比出积分差异
        $changeScore = $score - $this['score'];
        if ($changeScore) {
            $this->notifyScoreChange($changeScore, [
                'description' => '领卡积分同步',
            ]);
        }

        $this->save([
            'score' => $score,
            'used_score' => $usedScore,
            'total_score' => $score + $usedScore,
        ]);
    }

    /**
     * 加载卡券统计数据
     */
    protected function loadCardCount()
    {
        $user = $this->user;

        $totalCardCount = wei()->userWechatCard()->curApp()->count(['user_id' => $user['id']]);
        $cardCount = wei()->userWechatCard()->curApp()->count([
            'user_id' => $user['id'],
            'status' => UserWechatCardRecord::STATUS_NORMAL,
        ]);

        $this->fromArray([
            'card_count' => $cardCount,
            'total_card_count' => $totalCardCount,
            'used_card_count' => $totalCardCount - $cardCount,
        ]);
    }

    /**
     * 执行积分改变后的流程
     *
     * 1. 按需更新等级
     * 2. 将新的数据同步给微信
     *
     * @param int $score
     * @param array $data
     */
    public function notifyScoreChange($score, array $data)
    {
        $apiData = [
            'code' => $this['code'],
            'card_id' => $this['card_wechat_id'],
            'record_bonus' => $data['description'],
            'bonus' => $this['score'],
            'add_bonus' => $score,
        ];

        // 按需更新等级
        $extraData = $this->updateMemberLevel();
        if ($extraData) {
            $apiData += $extraData;
        }

        $api = wei()->wechatAccount->getCurrentAccount()->createApiService();
        $api->updateMemberCardUser($apiData);
    }

    /**
     * 按需更新等级,并返回微信接口所需的资料
     *
     * @return array
     */
    protected function updateMemberLevel()
    {
        // 如果指定了等级,暂不用更新
        if ($this['is_specified_level']) {
            return [];
        }

        $level = wei()->memberLevel->getLevelByScore($this['score']);
        if ($level['id'] == $this['level_id']) {
            return [];
        }

        $this['level_id'] = $level['id'];
        $this->save();

        $data = [];
        if ($level['image']) {
            $ret = wei()->wechatMedia->updateUrlToWechatUrlRet($level['image']);
            if ($ret['code'] === 1) {
                $data['background_pic_url'] = $data['url'];
            }
        }

        return $data;
    }
}
