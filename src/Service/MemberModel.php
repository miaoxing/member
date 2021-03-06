<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Member\Metadata\MemberTrait;
use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Model\HasAppIdTrait;

/**
 * MemberModel
 */
class MemberModel extends BaseModelV2
{
    use MemberTrait;
    use HasAppIdTrait;
}
