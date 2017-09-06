<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;
use Miaoxing\Plugin\Service\User;

class Member extends BaseService
{
    protected $members = [];

    public function __invoke()
    {
        return wei()->memberRecord();
    }

    /**
     * @param User $user
     * @return MemberRecord
     */
    public function getMember($user = null)
    {
        $user || $user = wei()->curUser;

        if (!isset($this->members[$user['id']])) {
            $this->members[$user['id']] = wei()->member()->curApp()->notDeleted()->findOrInit(['user_id' => $user['id']]);
        }

        return $this->members[$user['id']];
    }
}
