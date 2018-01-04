<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Service\User;
use Wei\RetTrait;

/**
 * 会员
 */
class Member extends BaseService
{
    use RetTrait;

    protected $members = [];

    /**
     * @return \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[]
     */
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
            $this->members[$user['id']] = wei()->member()
                ->curApp()
                ->notDeleted()
                ->andWhere(['card_id' => wei()->setting('member.default_card_id')])
                ->findOrInit(['user_id' => $user['id']]);
        }

        return $this->members[$user['id']];
    }
}
