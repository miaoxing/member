<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class MemberWeeklyStat extends BaseService
{
    public function __invoke()
    {
        return wei()->memberWeeklyStatRecord();
    }
}
