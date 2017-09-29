<?php

namespace Miaoxing\Member;

use Miaoxing\Member\Service\MemberRecord;
use Miaoxing\Member\Service\MemberStatLogRecord;
use Miaoxing\Order\Service\Order;
use miaoxing\plugin\BasePlugin;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Refund\Service\Refund;
use Miaoxing\WechatCard\Service\UserCard;
use Miaoxing\WechatCard\Service\UserWechatCardRecord;
use Miaoxing\WechatCard\Service\WechatCardRecord;
use Wei\RetTrait;
use Wei\WeChatApp;

class Plugin extends BasePlugin
{
    use RetTrait;

    /**
     * {@inheritdoc}
     */
    protected $name = '会员';

    /**
     * {@inheritdoc}
     */
    protected $description = '基于微信会员卡的会员系统';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $categories['member'] = [
            'name' => '会员',
            'sort' => 500,
        ];

        $subCategories['member'] = [
            'parentId' => 'member',
            'name' => '会员',
            'icon' => 'fa fa-credit-card',
        ];

        $navs[] = [
            'parentId' => 'member',
            'url' => 'admin/members',
            'name' => '会员管理',
            'sort' => 1000,
        ];

        $navs[] = [
            'parentId' => 'member',
            'url' => 'admin/member-levels',
            'name' => '会员等级',
        ];

        $navs[] = [
            'parentId' => 'member',
            'url' => 'admin/member-logs',
            'name' => '会员日志',
        ];

        $subCategories['member-setting'] = [
            'parentId' => 'member',
            'name' => '设置',
            'icon' => 'fa fa-gear',
            'sort' => 0,
        ];

        $navs[] = [
            'parentId' => 'member-setting',
            'url' => 'admin/member-settings',
            'name' => '功能设置',
        ];
    }

    /**
     * 更改积分后,同步到用户会员卡
     *
     * @param User $user
     * @param int $score
     * @param string $remark
     * @return array
     */
    public function onPostScoreChange(User $user, $score, $remark)
    {
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return $this->err('用户没有会员卡');
        }

        $member->incr('score', $score);
        if ($score > 0) {
            $member->incr('total_score', $score);
        } else {
            $member->incr('used_score', -$score);
        }
        $member->save();
    }

    public function onAsyncPostScoreChange($data)
    {
        // 如果没有会员卡,不用通知
        $user = wei()->user()->findById($data['user_id']);
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $apiData = [
            'code' => $member['code'],
            'card_id' => $member['card_wechat_id'],
            'record_bonus' => $data['data']['description'],
            'bonus' => $member['score'],
            'add_bonus' => $data['score'],
        ];

        // 按需更新等级
        $extraData = $this->updateMemberLevel($member);
        if ($extraData) {
            $apiData += $extraData;
        }

        $api = wei()->wechatAccount->getCurrentAccount()->createApiService();
        $api->updateMemberCardUser($apiData);
    }

    /**
     * 按需更新等级,并返回微信接口所需的资料
     *
     * @param MemberRecord $member
     * @return array
     */
    protected function updateMemberLevel(MemberRecord $member)
    {
        // 如果指定了等级,暂不用更新
        if ($member['is_specified_level']) {
            return [];
        }

        $level = wei()->memberLevel->getLevelByScore($member['score']);
        if ($level['id'] == $member['level_id']) {
            return [];
        }

        $member['level_id'] = $level['id'];
        $member->save();

        $data = [];
        if ($level['image']) {
            $ret = wei()->wechatMedia->updateUrlToWechatUrlRet($level['image']);
            if ($ret['code'] === 1) {
                $data['background_pic_url'] = $data['url'];
            }
        }

        return $data;
    }

    /**
     * 退款后退还积分
     *
     * @param Refund $refund
     */
    public function onAsyncRefundRefund(Refund $refund)
    {
        $user = $refund->getUser();
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $card = $member->wechatCard;
        $score = $card->calScore($refund['fee']);
        if (!$score) {
            return;
        }

        wei()->score->changeScore(-$score, [
            'description' => sprintf('退款%s元，扣除%s积分', $refund['fee'], $score),
        ], $user);
    }

    /**
     * 下单后增加积分
     *
     * @param Order $order
     */
    public function onAsyncPostOrderPay(Order $order)
    {
        $user = $order->getUser();
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $this->updateFirstConsume($member, $order);

        $card = $member->wechatCard;
        $score = $card->calScore($order['amount']);
        if (!$score) {
            return;
        }

        wei()->score->changeScore($score, [
            'description' => sprintf('消费%s元，获得%s积分', $order['amount'], $score),
        ], $user);
    }

    /**
     * 更新会员的首次消费时间
     *
     * @param MemberRecord $member
     * @param Order $order
     */
    public function updateFirstConsume(MemberRecord $member, Order $order)
    {
        if ($member['consumed_at'] && $member['consumed_at'] != '0000-00-00 00:00:00') {
            return;
        }

        $member->save(['consumed_at' => $order['payTime']]);

        wei()->memberStatLog()->setAppId()->save([
            'card_id' => $member['card_id'],
            'action' => MemberStatLogRecord::ACTION_FIRST_CONSUME,
        ]);
    }

    /**
     * 领卡后,更新会员卡中记录的卡券数量
     *
     * @param WechatCardRecord $card
     */
    public function onPostWechatUserGetCard(WechatCardRecord $card)
    {
        if ($card['type'] == WechatCardRecord::TYPE_MEMBER_CARD) {
            return;
        }

        $member = wei()->member->getMember();
        if ($member->isNew()) {
            return;
        }

        $member->incr('total_card_count', 1)
            ->incr('card_count', 1)
            ->save();
    }

    public function onPostWechatUserConsumeCard(UserWechatCardRecord $userCard)
    {
        $member = wei()->member->getMember();
        if ($member->isNew()) {
            return;
        }

        $member->decr('card_count', 1)
            ->incr('used_card_count', 1)
            ->save();
    }

    public function onWechatUserGiftingCard(WeChatApp $app, User $user)
    {
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $userCard = wei()->userWechatCard->getByCodeFromCache($app->getAttr('UserCardCode'));
        if (!$userCard) {
            return;
        }

        if (!$app->getAttr('IsReturnBack')) {
            // 转赠也是使用了该卡券,所以使用数增加,可用数减少
            $member->incr('used_card_count', 1)
                ->decr('card_count', 1);
        } else {
            // 退回则反之
            $member->incr('card_count', 1)
                ->decr('used_card_count', 1);
        }
        $member->save();
    }

    /**
     * 用户领取会员卡
     *
     * @param WeChatApp $app
     * @param User $user
     */
    public function onWechatUserGetCard(WeChatApp $app, User $user)
    {
        /** @var WechatCardRecord $card */
        $card = wei()->wechatCard->getByWechatIdFromCache($app->getAttr('CardId'));
        if (!$card || !$card->isMemberCard()) {
            return;
        }

        $member = wei()->member()->curApp()->findOrInit([
            'card_id' => $card['id'],
            'user_id' => $user['id'],
        ]);
//
//        if (!$member->isNew()) {
//            $this->logger->info('用户已有会员卡', $app->getAttrs());
//
//            return;
//        }

        // 如果是赠送,设置原来的卡号为无效
        if ($app->getAttr('IsGiveByFriend')) {
            $friendMember = wei()->member()->find(['card_code' => $app->getAttr('OldUserCardCode')]);
            /** @var MemberRecord $friendMember */
            if ($friendMember) {
                $friendMember->save([
                    'status' => UserWechatCardRecord::STATUS_UNAVAILABLE,
                    'unavailable_at' => date('Y-m-d H:i:s', $app->getCreateTime()),
                ]);
                $friendMember->softDelete();
            } else {
                $this->logger->warning('找不到赠送用户的原始会员卡', $app->getAttrs());
            }
        }

        // 新卡则初始化等级和积分等
        if ($member->isNew()) {
            $member->fromArray([
                'level_id' => wei()->setting->getValue('member.init_level_id', 0),
                'score' => (int) $card['bonus_rule']['init_increase_bonus'],
                'total_score' => (int) $card['bonus_rule']['init_increase_bonus'],
            ]);
        }

        /** @var MemberRecord $member */
        $member->save([
            'card_id' => $card['id'],
            'card_wechat_id' => $card['wechat_id'],
            'code' => $app->getAttr('UserCardCode'),
            'wechat_open_id' => $user['wechatOpenId'],
            'is_give_by_friend' => $app->getAttr('IsGiveByFriend'),
            'friend_user_name' => (string) $app->getAttr('FriendUserName'),
            'outer_str' => (string) $app->getAttr('OuterStr'),
            'status' => UserWechatCardRecord::STATUS_NORMAL, // 领卡则还原状态为正常
        ]);

        wei()->memberStatLog()->setAppId()->save([
            'card_id' => $member['card_id'],
            'action' => MemberStatLogRecord::ACTION_RECEIVE,
        ]);
    }

    /**
     * 用户删除卡券
     *
     * @param WeChatApp $app
     */
    public function onWechatUserDelCard(WeChatApp $app)
    {
        $card = wei()->wechatCard->getByWechatIdFromCache($app->getAttr('CardId'));
        if (!$card || !$card->isMemberCard()) {
            return;
        }

        $member = wei()->member()->find([
            'card_id' => $card['id'],
            'code' => $app->getAttr('UserCardCode'),
        ]);
        if (!$member) {
            $this->logger->info('Member card not found', ['data' => $app->getAttrs()]);
            return;
        }

        /** @var MemberRecord $member */
        $member['status'] = UserWechatCardRecord::STATUS_DELETE;
        $member->softDelete();
    }
}
