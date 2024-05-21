<?php

namespace IAWPSCOPED\Illuminate\Database\PDO;

use IAWPSCOPED\Doctrine\DBAL\Driver\AbstractSQLServerDriver;
/** @internal */
class SqlServerDriver extends AbstractSQLServerDriver
{
    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
    public function connect(array $params)
    {
        return new SqlServerConnection(new Connection($params['pdo']));
    }
}
