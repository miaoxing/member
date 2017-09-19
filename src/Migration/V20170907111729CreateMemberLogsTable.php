<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170907111729CreateMemberLogsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('member_logs')
            ->id()
            ->int('app_id')
            ->int('card_id')
            ->int('user_id')
            ->char('code', 12)->comment('会员卡号')
            ->string('action')->comment('操作')
            ->string('description')->comment('操作说明')
            ->timestamp('created_at')
            ->int('created_by')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('member_logs');
    }
}
