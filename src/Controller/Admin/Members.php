<?php

namespace Miaoxing\WechatMemberCard\Controller\Admin;

use miaoxing\plugin\BaseController;

class Members extends BaseController
{
    protected $controllerName = '会员管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '创建',
        'edit,update' => '编辑',
    ];

    public function indexAction()
    {

    }
}
