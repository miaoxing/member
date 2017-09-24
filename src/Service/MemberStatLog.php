<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class MemberStatLog extends BaseService
{
    public function __invoke()
    {
        return wei()->memberStatLogRecord();
    }
}
