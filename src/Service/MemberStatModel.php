<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Member\Metadata\MemberStatTrait;
use Miaoxing\Plugin\BaseModelV2;

/**
 * MemberStatModel
 */
class MemberStatModel extends BaseModelV2
{
    use MemberStatTrait;

    protected $data = [
        'receive_user' => 0,
        'receive_count' => 0,
        'first_consume_user' => 0,
        'total_receive_user' => 0,
        'total_receive_count' => 0,
        'total_first_consume_user' => 0,
    ];
}
