<?php

namespace IAWPSCOPED\Illuminate\Database\PDO;

use IAWPSCOPED\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use IAWPSCOPED\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
/** @internal */
class MySqlDriver extends AbstractMySQLDriver
{
    use ConnectsToDatabase;
}
