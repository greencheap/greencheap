<?php

namespace GreenCheap\Tests;

trait DbUtil
{
    /**
     * Gets a <b>real</b> database connection using the following parameters
     * of the $GLOBALS array:
     *
     * 'db_type' : The name of the Doctrine DBAL database driver to use.
     * 'db_username' : The username to use for connecting.
     * 'db_password' : The password to use for connecting.
     * 'db_host' : The hostname of the database to connect to.
     * 'db_name' : The name of the database to connect to.
     * 'db_port' : The port of the database to connect to.
     *
     * Usually these variables of the $GLOBALS array are filled by PHPUnit based
     * on an XML configuration file. If no such parameters exist, an SQLite
     * in-memory database is used.
     *
     * IMPORTANT:
     * 1) Each invocation of this method returns a NEW database connection.
     * 2) The database is dropped and recreated to ensure it's clean.
     *
     * @return Doctrine\DBAL\Connection The database connection instance.
     * @throws \Doctrine\DBAL\Exception
     */
    public function getConnection(): Doctrine\DBAL\Connection
    {
        if (isset($GLOBALS["db_type"], $GLOBALS["db_username"], $GLOBALS["db_password"], $GLOBALS["db_host"], $GLOBALS["db_name"], $GLOBALS["db_port"], $GLOBALS["tmpdb_type"], $GLOBALS["tmpdb_username"], $GLOBALS["tmpdb_password"], $GLOBALS["tmpdb_host"], $GLOBALS["tmpdb_name"], $GLOBALS["tmpdb_port"])) {
            $realDbParams = [
                "driver" => $GLOBALS["db_type"],
                "user" => $GLOBALS["db_username"],
                "password" => $GLOBALS["db_password"],
                "host" => $GLOBALS["db_host"],
                "dbname" => $GLOBALS["db_name"],
                "port" => $GLOBALS["db_port"],
            ];
            $tmpDbParams = [
                "driver" => $GLOBALS["tmpdb_type"],
                "user" => $GLOBALS["tmpdb_username"],
                "password" => $GLOBALS["tmpdb_password"],
                "host" => $GLOBALS["tmpdb_host"],
                "dbname" => $GLOBALS["tmpdb_name"],
                "port" => $GLOBALS["tmpdb_port"],
            ];

            $realConn = \Doctrine\DBAL\DriverManager::getConnection($realDbParams);

            $platform = $realConn->getDatabasePlatform();

            if ($platform->supportsCreateDropDatabase()) {
                $dbname = $realConn->getDatabase();
                // Connect to tmpdb in order to drop and create the real test db.
                $tmpConn = \Doctrine\DBAL\DriverManager::getConnection($tmpDbParams);
                $realConn->close();

                $tmpConn->getSchemaManager()->dropDatabase($dbname);
                $tmpConn->getSchemaManager()->createDatabase($dbname);

                $tmpConn->close();
            } else {
                $sm = $realConn->getSchemaManager();

                /* @var $schema Schema */
                $schema = $sm->createSchema();
                $stmts = $schema->toDropSql($realConn->getDatabasePlatform());

                foreach ($stmts as $stmt) {
                    try {
                        $realConn->executeStatement($stmt);
                    } catch (\Exception $e) {
                        // TODO: Now is this a real good idea?
                    }
                }
            }

            $conn = \Doctrine\DBAL\DriverManager::getConnection(array_merge(["wrapperClass" => "GreenCheap\Database\Connection"], $realDbParams), null, null);
        } else {
            $params = [
                "driver" => "pdo_sqlite",
                "memory" => true,
            ];
            if (isset($GLOBALS["db_path"])) {
                $params["path"] = $GLOBALS["db_path"];
                unlink($GLOBALS["db_path"]);
            }
            $conn = \Doctrine\DBAL\DriverManager::getConnection(array_merge(["wrapperClass" => "GreenCheap\Database\Connection"], $params));
        }

        return $conn;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getTempConnection()
    {
        $tmpDbParams = [
            "driver" => $GLOBALS["tmpdb_type"],
            "user" => $GLOBALS["tmpdb_username"],
            "password" => $GLOBALS["tmpdb_password"],
            "host" => $GLOBALS["tmpdb_host"],
            "dbname" => $GLOBALS["tmpdb_name"],
            "port" => $GLOBALS["tmpdb_port"],
        ];

        // Connect to tmpdb in order to drop and create the real test db.
        return \Doctrine\DBAL\DriverManager::getConnection($tmpDbParams);
    }

    public function getSharedConnection()
    {
        static $connection;
        static $error;

        if (!isset($connection) && !isset($error)) {
            try {
                $connection = $this->getConnection();
            } catch (\Exception $e) {
                $error = $e;
            }
        }

        if (isset($error)) {
            throw $error;
        }

        return $connection;
    }
}
