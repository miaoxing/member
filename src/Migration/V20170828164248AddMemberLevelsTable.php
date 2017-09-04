<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170828164248AddMemberLevelsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('member_levels')
            ->id()
            ->int('app_id')
            ->string('name', 32)
            ->string('image')
            ->bool('special')->comment('是否为特殊卡')
            ->int('start_score')
            ->int('end_score')
            ->smallInt('start_consume_times')
            ->smallInt('end_consume_times')
            ->decimal('discount', 3)
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
        $this->schema->dropIfExists('member_levels');
    }
}
