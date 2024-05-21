<?php

namespace IAWPSCOPED\Doctrine\Common\Cache\Psr6;

use InvalidArgumentException;
use IAWPSCOPED\Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;
/**
 * @internal
 */
final class InvalidArgument extends InvalidArgumentException implements PsrInvalidArgumentException
{
}
