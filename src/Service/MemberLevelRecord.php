<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseModel;

class MemberLevelRecord extends BaseModel
{
    protected $table = 'member_levels';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $updatedAtColumn = 'updated_at';

    protected $createdByColumn = 'created_by';

    protected $updatedByColumn = 'updated_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';

    public function afterSave()
    {
        wei()->cache->remove('member_levels:' . $this->app->getId());

        parent::beforeSave();
    }

    public function afterDestroy()
    {
        wei()->cache->remove('member_levels:' . $this->app->getId());

        parent::beforeSave();
    }
}
