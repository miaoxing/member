<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170826112806AddMembersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('members')
            ->id()
            ->int('app_id')
            ->int('card_id')->comment('微信卡券编号')
            ->string('card_wechat_id', 32)->comment('微信卡券的微信编号')
            ->char('code')->comment('用户领取到的code值')
            ->char('membership_number', 12)->comment('会员卡号')
            ->int('user_id')
            ->string('wechat_open_id', 32)
            ->int('level_id')->comment('等级')
            ->decimal('balance', 10)->comment('余额信息')
            ->tinyInt('status', 1)->comment('当前用户会员卡状态')
            ->text('field_list')->comment('开发者设置的会员卡会员信息类目，如等级')
            ->timestamp('consumed_at')->comment('首次消费时间')
            ->int('total_card_count')->comment('领取的优惠券数')
            ->int('used_card_count')->comment('使用的优惠券数')
            ->int('score')->comment('现有的积分')
            ->int('used_score')->comment('使用过的积分数')
            ->int('total_score')->comment('总的积分数')
            ->bool('is_give_by_friend')->comment('是否为转赠领取，1代表是，0代表否')
            ->string('outer_str', 16)->comment('领取场景值，用于领取渠道数据统计')
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('members');
    }
}
