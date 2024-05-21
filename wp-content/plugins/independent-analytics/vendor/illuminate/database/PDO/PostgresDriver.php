<?php

namespace IAWPSCOPED\Illuminate\Database\PDO;

use IAWPSCOPED\Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use IAWPSCOPED\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
/** @internal */
class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;
}
