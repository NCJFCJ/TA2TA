<?php

namespace IAWPSCOPED\Carbon\Doctrine;

use IAWPSCOPED\Doctrine\DBAL\Platforms\AbstractPlatform;
/** @internal */
class CarbonImmutableType extends DateTimeImmutableType implements CarbonDoctrineType
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'carbon_immutable';
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
