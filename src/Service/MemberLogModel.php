<?php

namespace Miaoxing\Member\Service;

use Miaoxing\Member\Metadata\MemberLogTrait;
use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\User\Model\BelongsToUserModelTrait;

/**
 * MemberLogModel
 */
class MemberLogModel extends BaseModelV2
{
    use MemberLogTrait;
    use BelongsToUserModelTrait;

    public function creator()
    {
        return $this->belongsTo('user', 'id', 'created_by');
    }
}
