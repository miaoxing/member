<?php

namespace Miaoxing\Member\Job;

use Miaoxing\Queue\Job;
use Miaoxing\Queue\Service\BaseJob;

class MemberUpdateLevel extends Job
{
    public function __invoke(BaseJob $job, $data)
    {
        $member = wei()->member()->findById($data['id']);

        wei()->event->trigger('async' . __CLASS__, [$member]);

        $job->delete();
    }
}
