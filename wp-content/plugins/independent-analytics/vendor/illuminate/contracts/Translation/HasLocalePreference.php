<?php

namespace IAWPSCOPED\Illuminate\Contracts\Translation;

/** @internal */
interface HasLocalePreference
{
    /**
     * Get the preferred locale of the entity.
     *
     * @return string|null
     */
    public function preferredLocale();
}
