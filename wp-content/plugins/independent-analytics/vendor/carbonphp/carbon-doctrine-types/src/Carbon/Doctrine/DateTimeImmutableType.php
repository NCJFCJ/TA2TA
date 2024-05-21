<?php

namespace IAWPSCOPED\Carbon\Doctrine;

use IAWPSCOPED\Carbon\CarbonImmutable;
use IAWPSCOPED\Doctrine\DBAL\Types\VarDateTimeImmutableType;
/** @internal */
class DateTimeImmutableType extends VarDateTimeImmutableType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<CarbonImmutable> */
    use CarbonTypeConverter;
    /**
     * @return class-string<CarbonImmutable>
     */
    protected function getCarbonClassName() : string
    {
        return CarbonImmutable::class;
    }
}
