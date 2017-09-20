<?php

namespace Miaoxing\Member\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170920205420AddFriendUserNameToMembersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('members')
            ->string('friend_user_name', 36)->after('is_give_by_friend')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('members')
            ->dropColumn('friend_user_name')
            ->exec();
    }
}
