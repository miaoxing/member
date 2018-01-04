<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseService;

/**
 * 会员日志
 */
class MemberLog extends BaseService
{
    public function __invoke()
    {
        return wei()->memberLogRecord();
    }
}
