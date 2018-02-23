<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20180223160844AddOutCodeToMembersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('members')
            ->string('out_code', 32)->after('code')->comment('外部卡号')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('members')
            ->dropColumn('out_code')
            ->exec();
    }
}
