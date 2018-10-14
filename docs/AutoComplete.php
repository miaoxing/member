<?php

namespace MiaoxingDoc\Member {

    /**
     * @property    \Miaoxing\Member\Service\Member $member 会员
     * @method      \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[] member()
     *
     * @property    \Miaoxing\Member\Service\MemberLevel $memberLevel 会员等级
     * @method      mixed memberLevel()
     *
     * @property    \Miaoxing\Member\Service\MemberLevelRecord $memberLevelRecord
     * @method      \Miaoxing\Member\Service\MemberLevelRecord|\Miaoxing\Member\Service\MemberLevelRecord[] memberLevelRecord()
     *
     * @property    \Miaoxing\Member\Service\MemberLog $memberLog 会员日志
     * @method      mixed memberLog()
     *
     * @property    \Miaoxing\Member\Service\MemberLogModel $memberLogModel MemberLogModel
     * @method      \Miaoxing\Member\Service\MemberLogModel|\Miaoxing\Member\Service\MemberLogModel[] memberLogModel()
     *
     * @property    \Miaoxing\Member\Service\MemberLogRecord $memberLogRecord
     * @method      \Miaoxing\Member\Service\MemberLogRecord|\Miaoxing\Member\Service\MemberLogRecord[] memberLogRecord()
     *
     * @property    \Miaoxing\Member\Service\MemberRecord $memberRecord
     * @method      \Miaoxing\Member\Service\MemberRecord|\Miaoxing\Member\Service\MemberRecord[] memberRecord()
     *
     * @property    \Miaoxing\Member\Service\MemberStatLog $memberStatLog 会员统计日志
     * @method      mixed memberStatLog()
     *
     * @property    \Miaoxing\Member\Service\MemberStatLogRecord $memberStatLogRecord
     * @method      \Miaoxing\Member\Service\MemberStatLogRecord|\Miaoxing\Member\Service\MemberStatLogRecord[] memberStatLogRecord()
     *
     * @property    \Miaoxing\Member\Service\MemberStatModel $memberStatModel MemberStatModel
     * @method      \Miaoxing\Member\Service\MemberStatModel|\Miaoxing\Member\Service\MemberStatModel[] memberStatModel()
     *
     * @property    \Miaoxing\Member\Service\MemberWeeklyStat $memberWeeklyStat
     * @method      mixed memberWeeklyStat()
     *
     * @property    \Miaoxing\Member\Service\MemberWeeklyStatRecord $memberWeeklyStatRecord
     * @method      \Miaoxing\Member\Service\MemberWeeklyStatRecord|\Miaoxing\Member\Service\MemberWeeklyStatRecord[] memberWeeklyStatRecord()
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

    /** @var Miaoxing\Member\Service\MemberLogModel $memberLogModel */
    $memberLog = wei()->memberLogModel();

    /** @var Miaoxing\Member\Service\MemberLogModel|Miaoxing\Member\Service\MemberLogModel[] $memberLogModels */
    $memberLogs = wei()->memberLogModel();

    /** @var Miaoxing\Member\Service\MemberLogRecord $memberLogRecord */
    $memberLogRecord = wei()->memberLogRecord;

    /** @var Miaoxing\Member\Service\MemberRecord $memberRecord */
    $memberRecord = wei()->memberRecord;

    /** @var Miaoxing\Member\Service\MemberStatLog $memberStatLog */
    $memberStatLog = wei()->memberStatLog;

    /** @var Miaoxing\Member\Service\MemberStatLogRecord $memberStatLogRecord */
    $memberStatLogRecord = wei()->memberStatLogRecord;

    /** @var Miaoxing\Member\Service\MemberStatModel $memberStatModel */
    $memberStat = wei()->memberStatModel();

    /** @var Miaoxing\Member\Service\MemberStatModel|Miaoxing\Member\Service\MemberStatModel[] $memberStatModels */
    $memberStats = wei()->memberStatModel();

    /** @var Miaoxing\Member\Service\MemberWeeklyStat $memberWeeklyStat */
    $memberWeeklyStat = wei()->memberWeeklyStat;

    /** @var Miaoxing\Member\Service\MemberWeeklyStatRecord $memberWeeklyStatRecord */
    $memberWeeklyStatRecord = wei()->memberWeeklyStatRecord;
}
