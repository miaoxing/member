<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170924090033CreateMemberStatsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('member_weekly_stats')
            ->id()
            ->int('app_id')
            ->int('card_id')
            ->date('stat_date')
            ->int('receive_user')
            ->int('receive_count')
            ->int('first_consume_user')
            ->int('total_receive_user')
            ->int('total_receive_count')
            ->int('total_first_consume_user')
            ->timestamps()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('member_weekly_stats');
    }
}
