<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class MemberLog extends BaseService
{
    public function __invoke()
    {
        return wei()->memberLogRecord();
    }
}
