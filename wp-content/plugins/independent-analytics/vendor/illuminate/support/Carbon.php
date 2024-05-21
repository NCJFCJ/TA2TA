<?php

namespace IAWPSCOPED\Illuminate\Support;

use IAWPSCOPED\Carbon\Carbon as BaseCarbon;
use IAWPSCOPED\Carbon\CarbonImmutable as BaseCarbonImmutable;
/** @internal */
class Carbon extends BaseCarbon
{
    /**
     * {@inheritdoc}
     */
    public static function setTestNow($testNow = null)
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }
}
