<?php

namespace plugins\member\docs {

    use Miaoxing\Member\Service\Member;
    use Miaoxing\Member\Service\MemberLevel;
    use Miaoxing\Member\Service\MemberLevelRecord;
    use Miaoxing\Member\Service\MemberRecord;

    /**
     * @property    Member $member 会员
     * @method      MemberRecord|MemberRecord[] member()
     *
     * @property    MemberLevel $memberLevel 会员等级
     * @method      MemberLevelRecord|MemberLevelRecord[] memberLevel()
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
