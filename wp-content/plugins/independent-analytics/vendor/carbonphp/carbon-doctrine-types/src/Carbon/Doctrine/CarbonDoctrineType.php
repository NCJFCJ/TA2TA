<?php

namespace IAWPSCOPED\Carbon\Doctrine;

use IAWPSCOPED\Doctrine\DBAL\Platforms\AbstractPlatform;
/** @internal */
interface CarbonDoctrineType
{
    public const MAXIMUM_PRECISION = 10;
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);
    public function convertToPHPValue($value, AbstractPlatform $platform);
    public function convertToDatabaseValue($value, AbstractPlatform $platform);
}
