<?php

namespace Miaoxing\Member\Service;

use miaoxing\plugin\BaseService;

class MemberLevel extends BaseService
{
    public function __invoke()
    {
        return wei()->memberLevelRecord();
    }

    /**
     * 根据积分获取所属的等级
     *
     * @param int $score
     * @return MemberLevelRecord|bool
     */
    public function getLevelByScore($score)
    {
        $levels = wei()->memberLevel()
            ->curApp()
            ->desc('score')
            ->cache(86400)
            ->tags(false)
            ->setCacheKey('member_levels:' . $this->app->getId())
            ->fetchAll();

        foreach ($levels as $level) {
            if ($level['special']) {
                continue;
            }

            if ($level['score'] <= $score) {
                return $level;
            }
        }

        return false;
    }
}
