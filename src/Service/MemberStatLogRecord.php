<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Service\User;

/**
 * @property User $user
 * @property User $creator
 */
class MemberStatLogRecord extends BaseModel
{
    const ACTION_RECEIVE = 1;

    const ACTION_FIRST_CONSUME = 2;

    protected $table = 'member_stat_logs';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    /**
     * @var array
     */
    protected $statFields = ['app_id', 'card_id'];

    /**
     * @var array
     */
    protected $statActions = [
        self::ACTION_RECEIVE => 'receive',
        self::ACTION_FIRST_CONSUME => 'first_consume',
    ];

    /**
     * @var bool
     */
    protected $statTotal = true;

    /**
     * @var array
     */
    protected $statSums = [];

    public function user()
    {
        return $this->belongsTo('user');
    }

    public function creator()
    {
        return $this->belongsTo('user', 'id', 'created_by');
    }
}
