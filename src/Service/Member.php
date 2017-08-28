<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class Member extends BaseService
{
    public function __invoke()
    {
        return wei()->memberRecord();
    }
}
