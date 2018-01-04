<?php

namespace Miaoxing\Member;

use Miaoxing\Address\Service\Address;
use Miaoxing\Member\Job\UserGetMemberCard;
use Miaoxing\Member\Service\MemberRecord;
use Miaoxing\Member\Service\MemberStatLogRecord;
use Miaoxing\Order\Service\Order;
use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Refund\Service\Refund;
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

    protected $adminNavId = 'member';

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

        $member->changeScore($score);
    }

    public function onAsyncPostScoreChange($data)
    {
        // 如果没有会员卡,不用通知
        $user = wei()->user()->findById($data['user_id']);
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $member->notifyScoreChange($data['score'], $data['data']);
    }

    /**
     * 退款后退还积分
     *
     * @param Refund $refund
     */
    public function onAsyncRefundRefund(Refund $refund)
    {
        $order = $refund->getOrder();
        $user = $order->getUser();
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return;
        }

        $card = $member->wechatCard;
        $score = $card->calScore($refund['fee']);
        if (!$score) {
            return;
        }

        $this->changeScoreByOrder(-$score, $order, [
            'description' => sprintf('退款%s元，扣除%s积分', $refund['fee'], $score),
        ]);
    }

    public function onPreOrderCreate(Order $order, Address $address = null, $data)
    {
        if (!$data['use_score']) {
            return;
        }

        $user = $order->getUser();
        $member = wei()->member->getMember($user);
        if ($member->isNew()) {
            return $this->err('您还没有会员卡,不能抵扣积分');
        }

        $ret = $member->wechatCard->calUseScore($user, $data['use_score'], $order->getCarts()->getProductAmount());
        if ($ret['code'] !== 1) {
            return $ret;
        }

        $order->setAmountRule('member_use_score', [
            'name' => '积分抵扣',
            'amountOff' => $ret['reduceMoney'],
            'useScore' => $data['use_score'],
        ]);
        $order->setConfig('member_use_score', [
            'use_score' => $data['use_score'],
            'reduce_money' => $ret['reduceMoney'],
        ]);
    }

    public function onPostOrderCreate(Order $order, $data)
    {
        $rule = $order->getAmountRule('member_use_score');
        if ($rule) {
            $this->changeScoreByOrder(-$rule['useScore'], $order, [
                'description' => sprintf('使用%s积分，抵扣%s元', $rule['useScore'], $rule['amountOff']),
            ]);
        }
    }

    public function onAdminOrdersShowItem()
    {
        $this->display();
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

        $this->changeScoreByOrder($score, $order, [
            'description' => sprintf('消费%s元，获得%s积分', $order['amount'], $score),
        ]);
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

        wei()->memberStatLog->create([
            'card_id' => $member['card_id'],
            'user_id' => $member['user_id'],
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
        // NOTE 使用卡券对应的用户,因为有可能是朋友核销共享的券
        $member = wei()->member->getMember($userCard->user);
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
            'code' => $app->getAttr('UserCardCode'),
        ]);

        // 新卡则初始化等级等
        $isNew = $member->isNew();
        if ($isNew) {
            $member->fromArray([
                'level_id' => wei()->setting->getValue('member.init_level_id', 0),
            ]);
        }

        // 还原卡则重置状态为正常
        if ($app->getAttr('IsRestoreMemberCard')) {
            $member->fromArray([
                'status' => UserWechatCardRecord::STATUS_NORMAL,
                'deleted_at' => '0000-00-00 00:00:00',
                'deleted_by' => 0,
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
        ]);

        wei()->queue->push(UserGetMemberCard::class, [
            'member_id' => $member['id'],
            'card_id' => $card['id'],
            'user_id' => $user['id'],
            'is_new' => $isNew,
            'attrs' => $app->getAttrs(),
        ], wei()->app->getNamespace());
    }

    public function onAsyncUserGetMemberCard($data)
    {
        // 如果是赠送,设置原来的卡号为无效
        $attrs = $data['attrs'];
        if ($attrs['IsGiveByFriend']) {
            $friendMember = wei()->member()->find(['code' => $attrs['OldUserCardCode']]);
            /** @var MemberRecord $friendMember */
            if ($friendMember) {
                $friendMember->save([
                    'status' => UserWechatCardRecord::STATUS_UNAVAILABLE,
                    'unavailable_at' => date('Y-m-d H:i:s', $attrs['CreateTime']),
                ]);
                $friendMember->softDelete();
            } else {
                $this->logger->warning('找不到赠送用户的原始会员卡', $attrs);
            }
        }

        // 2. 记录统计数据(还原卡则不记录为领取)
        if (!$attrs['IsRestoreMemberCard']) {
            wei()->memberStatLog->create([
                'card_id' => $data['card_id'],
                'user_id' => $data['user_id'],
                'action' => MemberStatLogRecord::ACTION_RECEIVE,
            ]);
        }

        // 3. 如果是新卡,发放赠送的积分
        /** @var MemberRecord $member */
        $member = wei()->member()->findById($data['member_id']);
        if ($data['is_new']) {
            $card = wei()->wechatCard()->findById($data['card_id']);
            if ($card['bonus_rule']['init_increase_bonus']) {
                wei()->score->changeScore($card['bonus_rule']['init_increase_bonus'], [
                    'description' => '开卡赠送',
                    'card_code' => $member['code'],
                ], $member->user);
            }
        }

        // 4. 将用户的积分,卡券数据同步到会员卡中
        $member->syncCountData();
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

    public function onUserWechatCardExpire(UserWechatCardRecord $card)
    {
        $member = wei()->member->getMember($card->user);
        if ($member->isNew()) {
            return;
        }

        $member->incr('used_card_count', 1)
            ->decr('card_count', 1)
            ->save();
    }

    public function onPostOrderCancel(Order $order)
    {
        if (!isset($order['config']['member_use_score'])) {
            return;
        }

        $useScore = $order['config']['member_use_score'];
        $this->changeScoreByOrder($useScore, $order, [
            'description' => sprintf('取消订单,返还抵扣的%s积分', $useScore['use_score']),
        ]);
    }

    protected function changeScoreByOrder($score, Order $order, $data)
    {
        $user = $order->getUser();
        $member = wei()->member->getMember($order->getUser());

        $data += [
            'order_id' => $order['id'],
            'shop_id' => $order['shopId'],
            'card_code' => $member['code'],
        ];

        return wei()->score->changeScore($score, $data, $user);
    }
}
