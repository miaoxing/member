<?php

namespace Miaoxing\Member\Controller\Admin;

use miaoxing\plugin\BaseController;
use Miaoxing\WechatCard\Service\WechatCardRecord;

class MemberSettings extends BaseController
{
    protected $controllerName = '会员设置';

    protected $actionPermissions = [
        'index,update' => '设置',
    ];

    protected $displayPageHeader = true;

    public function indexAction()
    {
        $cards = wei()->wechatCard()
            ->curApp()
            ->notDeleted()
            ->andWhere(['type' => WechatCardRecord::TYPE_MEMBER_CARD]);

        $levels = wei()->memberLevel()->curApp()->findAll();

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $this->setting->setValues((array) $req['settings'], ['member.']);

        return $this->suc();
    }
}
