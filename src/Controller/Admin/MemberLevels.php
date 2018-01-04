<?php

namespace Miaoxing\Member\Controller\Admin;

use Miaoxing\Plugin\BaseController;

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
                $memberLevels = wei()->memberLevel()
                    ->curApp()
                    ->notDeleted();

                $memberLevels
                    ->limit($req['rows'])
                    ->page($req['page'])
                    ->desc('id');

                // 数据
                $memberLevels->findAll();
                $data = [];
                foreach ($memberLevels as $memberLevel) {
                    $data[] = $memberLevel->toArray();
                }

                return $this->suc([
                    'data' => $data,
                    'page' => (int) $req['page'],
                    'rows' => (int) $req['rows'],
                    'records' => $memberLevels->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function editAction($req)
    {
        $memberLevel = wei()->memberLevel()->curApp()->notDeleted()->findId($req['id']);

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $memberLevel = wei()->memberLevel()->curApp()->notDeleted()->findId($req['id']);

        $memberLevel->save($req);

        return $this->suc();
    }

    public function destroyAction($req)
    {
        $memberLevel = wei()->memberLevel()->notDeleted()->findOneById($req['id']);

        $memberLevel->softDelete();

        return $this->suc();
    }

    public function getLevelAction($req)
    {
        $level = wei()->memberLevel->getLevelByScore($req['score']);

        return $this->suc(['data' => $level]);
    }
}
