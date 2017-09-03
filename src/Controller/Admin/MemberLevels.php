<?php

namespace Miaoxing\Member\Controller\Admin;

use miaoxing\plugin\BaseController;

class MemberLevels extends BaseController
{
    protected $controllerName = '会员等级管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '创建',
        'edit,update' => '编辑',
    ];

    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $members = wei()->member()
                    ->curApp()
                    ->notDeleted();

                $members
                    ->limit($req['rows'])
                    ->page($req['page']);

                if ($req['nick_name_user_id']) {
                    $members->andWhere(['user_id' => $req['nick_name_user_id']]);
                }

                if ($req['mobile_user_id']) {
                    $members->andWhere(['user_id' => $req['mobile_user_id']]);
                }

                if (wei()->isPresent($req['level'])) {
                    $members->andWhere(['level' => $req['level']]);
                }

                if ($req['start_date']) {
                    $members->andWhere('consumed_at >= ?', $req['start_date']);
                }

                if ($req['end_date']) {
                    $members->andWhere('consumed_at <= ?', $req['end_date']);
                }

                // 排序
                $sort = $req['sort'] ?: 'id';
                $order = $req['order'] == 'asc' ? 'ASC' : 'DESC';
                $members->orderBy($sort, $order);

                // 数据
                $members->findAll()->load(['user', 'memberLevel']);
                $data = [];
                foreach ($members as $member) {
                    $data[] = $member->toArray() + [
                            'level_name' => $member->memberLevel['name'],
                        ];
                }

                return $this->suc([
                    'data' => $data,
                    'page' => (int) $req['page'],
                    'rows' => (int) $req['rows'],
                    'records' => $members->count(),
                ]);

            default:
                $levels = wei()->memberLevel()->curApp()->findAll();

                return get_defined_vars();
        }
    }
}
