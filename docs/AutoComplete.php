<?php

namespace MiaoxingDoc\Member {

    /**
     * @property    \Miaoxing\Member\Service\Member $member 会员
     * @method      \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[] member()
     * @see         \Miaoxing\Member\Service\Member::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberLevel $memberLevel 会员等级
     * @method      mixed memberLevel()
     * @see         \Miaoxing\Member\Service\MemberLevel::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberLevelRecord $memberLevelRecord
     * @method      \Miaoxing\Member\Service\MemberLevelRecord|\Miaoxing\Member\Service\MemberLevelRecord[] memberLevelRecord()
     * @see         \Miaoxing\Member\Service\MemberLevelRecord::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberLog $memberLog 会员日志
     * @method      mixed memberLog()
     * @see         \Miaoxing\Member\Service\MemberLog::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberLogRecord $memberLogRecord
     * @method      \Miaoxing\Member\Service\MemberLogRecord|\Miaoxing\Member\Service\MemberLogRecord[] memberLogRecord()
     * @see         \Miaoxing\Member\Service\MemberLogRecord::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberRecord $memberRecord
     * @method      \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[] memberRecord()
     * @see         \Miaoxing\Member\Service\MemberRecord::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberStatLog $memberStatLog 会员统计日志
     * @method      mixed memberStatLog()
     * @see         \Miaoxing\Member\Service\MemberStatLog::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberStatLogRecord $memberStatLogRecord
     * @method      \Miaoxing\Member\Service\MemberStatLogRecord|\Miaoxing\Member\Service\MemberStatLogRecord[] memberStatLogRecord()
     * @see         \Miaoxing\Member\Service\MemberStatLogRecord::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberWeeklyStat $memberWeeklyStat
     * @method      mixed memberWeeklyStat()
     * @see         \Miaoxing\Member\Service\MemberWeeklyStat::__invoke
     *
     * @property    \Miaoxing\Member\Service\MemberWeeklyStatRecord $memberWeeklyStatRecord
     * @method      \Miaoxing\Member\Service\MemberWeeklyStatRecord|\Miaoxing\Member\Service\MemberWeeklyStatRecord[] memberWeeklyStatRecord()
     * @see         \Miaoxing\Member\Service\MemberWeeklyStatRecord::__invoke
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return MiaoxingDoc\Member\AutoComplete
     */
    function wei()
    {
    }

    /** @var Miaoxing\Member\Service\Member $member */
    $member = wei()->member;

    /** @var Miaoxing\Member\Service\MemberLevel $memberLevel */
    $memberLevel = wei()->memberLevel;

    /** @var Miaoxing\Member\Service\MemberLevelRecord $memberLevelRecord */
    $memberLevelRecord = wei()->memberLevelRecord;

    /** @var Miaoxing\Member\Service\MemberLog $memberLog */
    $memberLog = wei()->memberLog;

    /** @var Miaoxing\Member\Service\MemberLogRecord $memberLogRecord */
    $memberLogRecord = wei()->memberLogRecord;

    /** @var Miaoxing\Member\Service\MemberRecord $memberRecord */
    $memberRecord = wei()->memberRecord;

    /** @var Miaoxing\Member\Service\MemberStatLog $memberStatLog */
    $memberStatLog = wei()->memberStatLog;

    /** @var Miaoxing\Member\Service\MemberStatLogRecord $memberStatLogRecord */
    $memberStatLogRecord = wei()->memberStatLogRecord;

    /** @var Miaoxing\Member\Service\MemberWeeklyStat $memberWeeklyStat */
    $memberWeeklyStat = wei()->memberWeeklyStat;

    /** @var Miaoxing\Member\Service\MemberWeeklyStatRecord $memberWeeklyStatRecord */
    $memberWeeklyStatRecord = wei()->memberWeeklyStatRecord;
}
