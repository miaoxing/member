<?php

namespace Miaoxing\Member\Controller\Cli;

use miaoxing\plugin\BaseController;

class Members extends BaseController
{
    public function statAction($req)
    {
        // 1. 获取统计的时间范围
        $stat = wei()->statV2;
        $today = $stat->getFirstDayOfWeek();
        list($startDate, $endDate) = explode('~', (string) $req['date']);
        if (!$startDate) {
            $startDate = $today;
        }
        if (!$endDate) {
            $endDate = $startDate;
        }

        // 2. 读取各天统计数据
        $logs = $stat->createQuery('memberStatLogRecord', $startDate, $endDate);
        $logs = $logs->fetchAll();

        // 3. 按日期,编号格式化
        $data = $stat->format('memberStatLogRecord', $logs);

        // 4. 记录到统计表中
        $stat->save('memberStatLogRecord', $data, 'memberWeeklyStatRecord');

        return $this->suc();
    }
}
