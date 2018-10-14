<?php

namespace Miaoxing\Member\Controller\Admin;

use DateTime;
use Miaoxing\Plugin\BaseController;

class MemberStats extends BaseController
{
    protected $controllerName = '会员统计';

    protected $actionPermissions = [
        'show' => '查看',
    ];

    protected $displayPageHeader = true;

    public function showAction($req)
    {
        $card = wei()->wechatCard()->curApp()->findOneById($req['card_id']);

        // 获取查询的日期范围
        $startDate = $req['startDate'] ?: date('Y-m-d', strtotime('-7 days'));
        $endDate = $req['endDate'] ?: date('Y-m-d');

        switch ($req['_format']) {
            case 'json':
                // 1. 读出统计数据
                $stats = wei()->memberStatModel()
                    ->andWhere(['card_id' => $card['id']])
                    ->andWhere('stat_date BETWEEN ? AND ? ', [$startDate, $endDate])
                    ->fetchAll();

                // 2. 如果取出的数据和日期不一致,补上缺少的数据
                $date1 = new DateTime($startDate);
                $date2 = new DateTime($endDate);
                $dateCount = $date1->diff($date2)->days + 1;
                if (count($stats) != $dateCount) {
                    // 找到最后一个有数据的日期
                    $lastStat = wei()->memberStatModel()
                        ->andWhere('stat_date < ?', $startDate)
                        ->desc('id')
                        ->toArray();

                    $defaults = wei()->memberStatModel->getData();

                    $stats = wei()->statV2->normalize(
                        'memberLogModel',
                        $stats,
                        $defaults,
                        $lastStat,
                        $startDate,
                        $endDate
                    );
                }

                // 3. 转换为数字
                $stats = wei()->chart->convertNumbers($stats);

                return $this->suc([
                    'data' => $stats,
                ]);

            default:
                return get_defined_vars();
        }
    }
}
