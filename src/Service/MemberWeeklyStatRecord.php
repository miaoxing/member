<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseModel;

class MemberWeeklyStatRecord extends BaseModel
{
    protected $table = 'member_weekly_stats';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createAtColumn = 'created_at';

    protected $updateAtColumn = 'updated_at';

    protected $data = [
        'receive_user' => 0,
        'receive_count' => 0,
        'first_consume_user' => 0,
        'total_receive_user' => 0,
        'total_receive_count' => 0,
        'total_first_consume_user' => 0,
    ];
}
