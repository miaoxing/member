<?php

namespace Miaoxing\Member\Controller\Admin;

use miaoxing\plugin\BaseController;

class Members extends BaseController
{
    protected $controllerName = '会员管理';

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

                if (wei()->isPresent($req['level_id'])) {
                    $members->andWhere(['level_id' => $req['level_id']]);
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
                            'user' => $member->user,
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

    public function updateLevelAction($req)
    {
        $member = wei()->member()->curApp()->findOneById($req['id']);
        $level = $member->memberLevel;

        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'level_id' => [
                    'notEqualTo' => $member['level_id'],
                ],
                'description' => [],
            ],
            'names' => [
                'level_id' => '等级',
                'description' => '更改说明',
            ],
            'messages' => [
                'level_id' => [
                    'notEqualTo' => '%name%未改变',
                ],
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        // 判断是否指定了和积分范围不一致的等级
        $realLevelId = wei()->memberLevel->getLevelByScore($member['score']);
        $isSpecifiedLevel = $realLevelId == $req['level_id'];

        $member->save([
            'level_id' => $req['level_id'],
            'is_specified_level' => $isSpecifiedLevel,
        ]);
        unset($member->memberLevel);
        $newLevel = $member->memberLevel;

        wei()->memberLog()->setAppId()->save([
            'card_id' => $member['card_id'],
            'user_id' => $member['user_id'],
            'code' => $member['code'],
            'action' => sprintf('将等级从"%s"更改为"%s"', $level['name'], $newLevel['name']),
            'description' => $req['description'],
        ]);

        return $this->suc();
    }
}
