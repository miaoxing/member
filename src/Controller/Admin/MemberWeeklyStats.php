<?php

namespace Miaoxing\Member\Controller\Admin;

use DateTime;
use Miaoxing\Plugin\BaseController;

class MemberWeeklyStats extends BaseController
{
    protected $controllerName = '会员每周统计';

    protected $actionPermissions = [
        'show' => '查看',
    ];

    protected $displayPageHeader = true;

    public function showAction($req)
    {
        $card = wei()->wechatCard()->curApp()->findOneById($req['card_id']);

        // 获取查询的日期范围
        $startDate = $req['start_date'] ?: date('Y-m-d', strtotime('-8 weeks'));
        $endDate = $req['end_date'] ?: date('Y-m-d');
        $startDate = wei()->statV2->getFirstDayOfWeek(strtotime($startDate));
        $endDate = wei()->statV2->getFirstDayOfWeek(strtotime($endDate));

        switch ($req['_format']) {
            case 'json':
                // 1. 读出统计数据
                $stats = wei()->memberWeeklyStat()
                    ->andWhere(['card_id' => $card['id']])
                    ->andWhere('stat_date BETWEEN ? AND ? ', [$startDate, $endDate])
                    ->fetchAll();

                // 2. 如果取出的数据和日期不一致,补上缺少的数据
                $date1 = new DateTime($startDate);
                $date2 = new DateTime($endDate);
                $dateCount = $date1->diff($date2)->days + 1;
                if (count($stats) != $dateCount) {
                    // 找到最后一个有数据的日期
                    $lastStat = wei()->memberWeeklyStat()
                        ->andWhere('stat_date < ?', $startDate)
                        ->desc('id')
                        ->toArray();

                    $defaults = wei()->memberWeeklyStatRecord->getData();

                    $stats = wei()->statV2->normalize(
                        'memberWeeklyStatRecord',
                        $stats,
                        $defaults,
                        $lastStat,
                        $startDate,
                        $endDate,
                        '+1 week'
                    );
                }

                // 3. 转换为数字
                $stats = wei()->chart->convertNumbers($stats);
                foreach ($stats as &$stat) {
                    $stat['stat_week'] = wei()->statV2->getWeekNumber($stat['stat_date'])
                        . '（' . $stat['stat_date'] . '）';
                }

                return $this->suc([
                    'data' => $stats,
                ]);

            default:
                return get_defined_vars();
        }
    }
}
