<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Member\Job\MemberUpdateLevel;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Service\User;
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

    protected $createdAtColumn = 'created_at';

    protected $updatedAtColumn = 'updated_at';

    protected $createdByColumn = 'created_by';

    protected $updatedByColumn = 'updated_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';

    protected $userIdColumn = 'user_id';

    protected $labels = [
        'code' => '卡号',
        'level_id' => '等级',
        'consumed_at' => '首次消费时间',
        'total_card_count' => '领取的优惠券数',
        'used_card_count' => '使用的优惠券数',
        'score' => '现有积分数',
        'used_score' => '使用过的积分数',
        'total_score' => '总的积分数',
    ];

    public function getLabels()
    {
        return $this->labels;
    }

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

        // 计算出各项积分值
        $user = $this->user;
        $score = wei()->score->getScore($user);
        $usedScore = wei()->scoreLog()
            ->curApp()
            ->select('SUM(score)')
            ->fetchColumn([
                'user_id' => $user['id'],
                'action' => ScoreLogRecord::ACTION_SUB,
            ]);
        $changeScore = $score - $this['score'];

        // 保存最新的积分数据
        $this->save([
            'score' => $score,
            'used_score' => $usedScore,
            'total_score' => $score + $usedScore,
        ]);

        // 再将积分差异同步给微信
        if ($changeScore) {
            $this->notifyScoreChange($changeScore, [
                'description' => '领卡积分同步',
            ]);
        }
    }

    /**
     * 加载卡券统计数据
     */
    protected function loadCardCount()
    {
        $user = $this->user;
        $supportTypes = wei()->wechatCard->getSupportTypes();

        $totalCardCount = wei()->userWechatCard()->curApp()->count([
            'user_id' => $user['id'],
            'type' => $supportTypes,
        ]);
        $cardCount = wei()->userWechatCard()->curApp()->count([
            'user_id' => $user['id'],
            'type' => $supportTypes,
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

        wei()->queue->push(MemberUpdateLevel::class, ['id' => $this['id']], wei()->app->getNamespace());

        return $data;
    }

    public function changeScore($score)
    {
        $realScore = $this['score'] + $score;

        $this->incr('score', $score);
        if ($score > 0) {
            $this->incr('total_score', $score);
        } else {
            $this->incr('used_score', -$score);
        }
        $this->save();
        $this->setRawValue('score', $realScore);
    }

    /**
     * 更新领卡的统计
     */
    public function updateAddCardStat()
    {
        $this->incr('card_count', 1)->incr('total_card_count', 1)->save();
    }

    /**
     * 更新使用卡的统计
     */
    public function updateUseCardStat()
    {
        $this->decr('card_count', 1)->incr('used_card_count', 1)->save();
    }
}
