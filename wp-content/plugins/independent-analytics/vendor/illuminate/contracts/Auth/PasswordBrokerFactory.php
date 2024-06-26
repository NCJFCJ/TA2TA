<?php

namespace IAWPSCOPED\Illuminate\Contracts\Auth;

/** @internal */
interface PasswordBrokerFactory
{
    /**
     * Get a password broker instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function broker($name = null);
}
