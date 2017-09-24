<?php

namespace plugins\member\docs {

    use Miaoxing\Member\Service\Member;
    use Miaoxing\Member\Service\MemberLevel;
    use Miaoxing\Member\Service\MemberLevelRecord;
    use Miaoxing\Member\Service\MemberLog;
    use Miaoxing\Member\Service\MemberLogRecord;
    use Miaoxing\Member\Service\MemberRecord;
    use Miaoxing\Member\Service\MemberScoreLog;
    use Miaoxing\Member\Service\MemberScoreLogRecord;
    use Miaoxing\Member\Service\MemberStatLog;
    use Miaoxing\Member\Service\MemberStatLogRecord;
    use Miaoxing\Member\Service\MemberWeeklyStat;
    use Miaoxing\Member\Service\MemberWeeklyStatRecord;

    /**
     * @property    Member $member 会员
     * @method      MemberRecord|MemberRecord[] member()
     *
     * @property    MemberLevel $memberLevel 会员等级
     * @method      MemberLevelRecord|MemberLevelRecord[] memberLevel()
     *
     * @property    MemberLog $memberLog 会员日志
     * @method      MemberLogRecord|MemberLogRecord[] memberLog()
     *
     * @property    MemberStatLog $memberStatLog 会员统计日志
     * @method      MemberStatLogRecord|MemberStatLogRecord[] memberStatLog()
     *
     * @property    MemberWeeklyStat $memberStat 会员统计
     * @method      MemberWeeklyStatRecord|MemberWeeklyStatRecord[] memberWeeklyStat()
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return \plugins\member\docs\AutoComplete
     */
    function wei()
    {
    }
}
