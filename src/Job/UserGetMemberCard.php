<?php

namespace Miaoxing\Member\Job;

use Miaoxing\Queue\Job;
use Miaoxing\Queue\Service\BaseJob;

class UserGetMemberCard extends Job
{
    public function __invoke(BaseJob $job, $data)
    {
        wei()->event->trigger('asyncUserGetMemberCard', [$data]);

        $job->delete();
    }
}

