<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;
use Miaoxing\Plugin\Service\User;
use Wei\RetTrait;

class Member extends BaseService
{
    use RetTrait;

    protected $members = [];

    public function __invoke()
    {
        return wei()->memberRecord();
    }

    /**
     * @param User $user
     * @return MemberRecord
     * @todo 优先获取默认的会员卡?
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
