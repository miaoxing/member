<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170924090027CreateMemberStatLogsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('member_stat_logs')
            ->id()
            ->int('app_id')
            ->int('card_id')
            ->int('user_id')
            ->tinyInt('action')
            ->date('created_date')
            ->timestamp('created_at')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('member_stat_logs');
    }
}
