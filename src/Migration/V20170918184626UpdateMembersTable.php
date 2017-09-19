<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170918184626UpdateMembersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('members')
            ->int('card_count')->comment('现有的优惠券数')->after('consumed_at')
            ->bool('is_specified_level')->comment('是否指定了等级')->after('level_id')
            ->char('code', 12)->comment('会员卡号')->change()
            ->decimal('balance', 10)->unsigned(false)->change()
            ->int('score')->unsigned(false)->change()
            ->dropColumn('membership_number')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('members')
            ->dropColumn('card_count')
            ->dropColumn('is_specified_level')
            ->char('code')->comment('用户领取到的code值')->change()
            ->decimal('balance', 10)->change()
            ->int('score')->change()
            ->char('membership_number', 12)->comment('会员卡号')->after('code')
            ->exec();
    }
}
