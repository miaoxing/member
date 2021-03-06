<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Plugin\BaseService;

/**
 * 会员统计日志
 */
class MemberStatLog extends BaseService
{
    public function __invoke()
    {
        return wei()->memberStatLogRecord();
    }

    public function create(array $data)
    {
        if (wei()->member->statType == 'weekly') {
            $date = $this->getFirstDayOfWeek();
        } else {
            $date = wei()->time->today();
        }

        return wei()->memberStatLog()->setAppId()->save($data + [
                'created_date' => $date,
            ]);
    }

    public function getFirstDayOfWeek($now = null)
    {
        // 传入null会认为是0,默认传入当前时间
        $now || $now = time();

        return date('Y-m-d', strtotime('-' . date('w', $now) . ' days', $now));
    }
}
