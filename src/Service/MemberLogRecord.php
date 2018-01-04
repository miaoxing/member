<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Service\User;

/**
 * @property User $user
 * @property User $creator
 */
class MemberLogRecord extends BaseModel
{
    protected $table = 'member_logs';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    public function user()
    {
        return $this->belongsTo('user');
    }

    public function creator()
    {
        return $this->belongsTo('user', 'id', 'created_by');
    }
}
