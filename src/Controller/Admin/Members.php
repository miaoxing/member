<?php

namespace Miaoxing\Member\Controller\Admin;

use Miaoxing\Member\Job\MemberUpdateLevel;
use Miaoxing\Member\Service\MemberRecord;
use Miaoxing\Plugin\BaseController;

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
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        switch ($req['_format']) {
            case 'json':
            case 'csv':
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

                if ($req['card_code']) {
                    $members->andWhere('code LIKE ?', '%' . $req['card_code'] . '%');
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

                /** @var MemberRecord $member */
                foreach ($members as $member) {
                    $data[] = $member->toArray() + [
                            'user' => $member->user,
                            'user_nick_name' => $member->user->getNickName(),
                            'level_name' => $member->memberLevel['name'],
                        ];
                }

                if ($req['_format'] == 'csv') {
                    return $this->renderCsv($data);
                } else {
                    return $this->suc([
                        'data' => $data,
                        'page' => (int) $req['page'],
                        'rows' => (int) $req['rows'],
                        'records' => $members->count(),
                    ]);
                }

                // no break
            default:
                $levels = wei()->memberLevel()->curApp()->findAll();

                return get_defined_vars();
        }
    }

    protected function renderCsv($members)
    {
        $labels = wei()->memberRecord->getLabels();
        $data = [];
        $data[0] = [
            '用户',
            '手机号',
            $labels['code'],
            $labels['level_id'],
            $labels['consumed_at'],
            $labels['total_card_count'],
            $labels['used_card_count'],
            $labels['score'],
            $labels['used_score'],
            $labels['total_score'],
        ];

        foreach ($members as $member) {
            $rowData = [
                $member['user_nick_name'] . ' ',
                $member['user']['mobile'],
                $member['code'],
                $member['level_name'],
                $member['consumed_at'],
                $member['total_card_count'],
                $member['used_card_count'],
                $member['score'],
                $member['used_score'],
                $member['total_score'],
            ];

            $newRowData = [];
            foreach ($rowData as $row) {
                $newRowData[] = str_replace(',', ' ', $row . '');
            }
            $data[] = $newRowData;
        }

        return wei()->csvExporter->export('members', $data);
    }

    public function updateLevelAction($req)
    {
        $member = wei()->member()->curApp()->findOneById($req['id']);
        $level = $member->memberLevel;

        $ret = wei()->v()
            ->key('level_id', '等级')
            ->notEqualTo($member['level_id'])->message('%name%未改变')
            ->key('description', '更改说明')
            ->check($req);
        if ($ret['code'] !== 1) {
            return $ret;
        }

        // 判断是否指定了和积分范围不一致的等级
        $realLevel = wei()->memberLevel->getLevelByScore($member['score']);
        $isSpecifiedLevel = $realLevel['id'] != $req['level_id'];

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

        $member->syncMemberLevel();

        return $this->suc();
    }
}
