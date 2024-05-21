<?php

declare (strict_types=1);
namespace IAWPSCOPED\Doctrine\Inflector;

/** @internal */
interface WordInflector
{
    public function inflect(string $word) : string;
}
