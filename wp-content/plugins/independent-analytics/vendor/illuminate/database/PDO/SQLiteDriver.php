<?php

namespace IAWPSCOPED\Illuminate\Database\PDO;

use IAWPSCOPED\Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use IAWPSCOPED\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
/** @internal */
class SQLiteDriver extends AbstractSQLiteDriver
{
    use ConnectsToDatabase;
}
