<?php

namespace IAWPSCOPED\Illuminate\Contracts\Container;

use Exception;
use IAWPSCOPED\Psr\Container\ContainerExceptionInterface;
/** @internal */
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
