<?php

namespace IAWPSCOPED\Carbon\Doctrine;

use IAWPSCOPED\Carbon\Carbon;
use IAWPSCOPED\Doctrine\DBAL\Types\VarDateTimeType;
/** @internal */
class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<Carbon> */
    use CarbonTypeConverter;
}
