<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class Member extends BaseService
{
    public function __invoke()
    {
        return wei()->memberRecord();
    }

    public function getMember()
    {
        return wei()->member()->curApp()->notDeleted()->findOrInit(['user_id' => $user['id']]);
    }
}
