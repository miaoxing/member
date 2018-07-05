<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20180705222229AddLastConsumedAtToMembersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('members')
            ->timestamp('last_consumed_at')->after('consumed_at')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('members')
            ->dropColumn('last_consumed_at')
            ->exec();
    }
}
