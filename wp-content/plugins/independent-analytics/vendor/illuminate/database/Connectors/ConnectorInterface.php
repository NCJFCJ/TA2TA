<?php

namespace IAWPSCOPED\Illuminate\Database\Connectors;

/** @internal */
interface ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config);
}
