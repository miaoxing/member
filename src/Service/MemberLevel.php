<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class MemberLevel extends BaseService
{
    public function __invoke()
    {
        return wei()->memberLevelRecord();
    }
}
