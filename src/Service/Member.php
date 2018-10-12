<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Config\ConfigTrait;
use Miaoxing\Member\Plugin;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Service\User;
use Wei\RetTrait;

/**
 * 会员
 *
 * @property bool $enableLevel
 * @property bool $enableWechatCard
 */
class Member extends BaseService
{
    use RetTrait;
    use ConfigTrait;

    protected $configs = [
        'enableLevel' => [
            'default' => true,
        ],
        'enableWechatCard' => [
            'default' => true,
        ],
    ];

    protected $members = [];

    /**
     * @var bool
     */
    protected $notify;

    /**
     * @return \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[]
     */
    public function __invoke()
    {
        return wei()->memberRecord();
    }

    /**
     * @param User $user
     * @return MemberRecord
     */
    public function getMember($user = null)
    {
        $user || $user = wei()->curUser;

        if (!isset($this->members[$user['id']])) {
            $this->members[$user['id']] = wei()->member()
                ->curApp()
                ->notDeleted()
                ->andWhere(['card_id' => wei()->setting('member.default_card_id')])
                ->findOrInit(['user_id' => $user['id']]);
        }

        return $this->members[$user['id']];
    }

    public function isNotify()
    {
        return $this->notify;
    }

    public function syncMember(User $user)
    {
        $cardId = wei()->setting('member.default_card_id');
        $wechatCard = wei()->wechatCard()->findOneById($cardId);

        $api = wei()->wechatAccount->getCurrentAccount()->createApiService();
        $ret = $api->getUserCardList([
            'openid' => $user['wechatOpenId'],
            'card_id' => $wechatCard['wechat_id'],
        ]);
        if ($ret['code'] !== 1) {
            return $ret;
        }

        if (!isset($ret['card_list'][0])) {
            return $this->err('没有读取到会员卡');
        }

        // 模拟数据,调用 onWechatUserGetCard
        $code = $ret['card_list'][0]['code'];
        $app = wei()->weChatApp;
        $app->setOption('attrs', [
            'CardId' => $wechatCard['wechat_id'],
            'UserCardCode' => $code,
        ]);

        /** @var Plugin $plugin */
        $plugin = wei()->plugin->getOneById('member');
        $plugin->onWechatUserGetCard($app, $user);
    }

    public function findOrSyncMember(User $user)
    {
        $member = $this->getMemberWithDeleted($user);
        if ($member->isNew()) {
            unset($this->members[$user['id']]);
            $this->syncMember($user);
        }

        return $this->getMemberWithDeleted($user);
    }

    /**
     * @param $user
     * @return MemberRecord
     */
    protected function getMemberWithDeleted($user)
    {
        return wei()->member()
            ->curApp()
            ->andWhere(['card_id' => wei()->setting('member.default_card_id')])
            ->findOrInit(['user_id' => $user['id']]);
    }
}
