<?php

namespace Miaoxing\Member;

use Miaoxing\Member\Service\MemberRecord;
use Miaoxing\Order\Service\Order;
use miaoxing\plugin\BasePlugin;
use Miaoxing\Plugin\Service\User;
use Wei\RetTrait;

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
    protected $description = '包括会员卡等';

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
            $data['background_pic_url'] = $level['image'];
        }

        return $data;
    }

    /**
     * 下单后增加积分
     */
    public function onOrderPay()
    {

    }

    /**
     * 退款后退还积分
     */
    public function onRefund()
    {

    }

    public function onAsyncPostOrderPay(Order $order)
    {
    }
}
