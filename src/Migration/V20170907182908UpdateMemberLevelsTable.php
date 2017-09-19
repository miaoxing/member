<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170907182908UpdateMemberLevelsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('member_levels')
            ->dropColumn('start_score')
            ->dropColumn('end_score')
            ->dropColumn('start_consume_times')
            ->dropColumn('end_consume_times')
            ->int('score')->after('special')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('member_levels')
            ->int('start_score')
            ->int('end_score')
            ->smallInt('start_consume_times')
            ->smallInt('end_consume_times')
            ->dropColumn('score')
            ->exec();
    }
}
