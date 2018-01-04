<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseService;

class MemberWeeklyStat extends BaseService
{
    public function __invoke()
    {
        return wei()->memberWeeklyStatRecord();
    }
}
