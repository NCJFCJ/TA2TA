<?php

namespace IAWPSCOPED\Carbon\Doctrine;

use IAWPSCOPED\Doctrine\DBAL\Platforms\AbstractPlatform;
/** @internal */
class CarbonType extends DateTimeType implements CarbonDoctrineType
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'carbon';
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return \true;
    }
}
