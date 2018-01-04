<?php

namespace Miaoxing\Member\Controller\Admin;

use Miaoxing\Plugin\BaseController;

class MemberLogs extends BaseController
{
    protected $controllerName = '会员日志管理';

    protected $actionPermissions = [
        'index' => '列表',
    ];

    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $memberLogs = wei()->memberLog()
                    ->curApp();

                $memberLogs
                    ->limit($req['rows'])
                    ->page($req['page'])
                    ->desc('id');

                if ($req['created_by']) {
                    $memberLogs->andWhere(['created_by' => $req['created_by']]);
                }

                // TODO 解决code和微信冲突
                if ($req['card_code']) {
                    $memberLogs->andWhere(['code' => $req['card_code']]);
                }

                if ($req['start_date']) {
                    $memberLogs->andWhere('created_at >= ?', $req['start_date']);
                }

                if ($req['end_date']) {
                    $memberLogs->andWhere('created_at <= ?', $req['end_date'] . '23:59:59');
                }

                // 数据
                $memberLogs->findAll()->load(['user', 'creator']);
                $data = [];
                foreach ($memberLogs as $memberLog) {
                    $data[] = $memberLog->toArray() + [
                            'user' => $memberLog->user,
                            'creator' => $memberLog->creator,
                        ];
                }

                return $this->suc([
                    'data' => $data,
                    'page' => (int) $req['page'],
                    'rows' => (int) $req['rows'],
                    'records' => $memberLogs->count(),
                ]);

            default:

                return get_defined_vars();
        }
    }
}
