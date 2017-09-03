<?php

namespace Miaoxing\Member\Controller\Admin;

use miaoxing\plugin\BaseController;

class MemberSettings extends BaseController
{
    protected $controllerName = '会员设置';

    protected $actionPermissions = [
        'index,update' => '设置',
    ];

    protected $displayPageHeader = true;

    public function indexAction()
    {
        $levels = wei()->memberLevel()->curApp()->findAll();

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $this->setting->setValues((array) $req['settings'], ['member.']);

        return $this->suc();
    }
}
