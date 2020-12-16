<?php

namespace GreenCheap\Database;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as BaseConnection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Exception;

class Connection extends BaseConnection
{
    const SINGLE_QUOTED_TEXT = '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'';
    const DOUBLE_QUOTED_TEXT = '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"';

    /**
     * The database utility.
     *
     * @var Utility
     */
    protected $utility;

    /**
     * The table prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The table prefix placeholder.
     *
     * @var string
     */
    protected string $placeholder = '@';

    /**
     * The regex for parsing SQL query parts.
     *
     * @var array
     */
    protected $regex;

    /**
     * Initializes a new instance of the Connection class.
     *
     * @param array $params
     * @param Driver $driver
     * @param Configuration|null $config
     * @param EventManager|null $eventManager
     * @throws Exception
     */
    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        if (!isset($params['defaultTableOptions'])) {
            $params['defaultTableOptions'] = [];
        }

        foreach (['engine', 'charset', 'collate'] as $name) {
            if (isset($params[$name])) {
                $params['defaultTableOptions'][$name] = $params[$name];
                unset($params[$name]);
            }
        }

        if (isset($params['prefix'])) {
            $this->prefix = $params['prefix'];
        }

        $this->regex = [
            'quotes' => "/([^'\"]+)(?:".self::DOUBLE_QUOTED_TEXT."|".self::SINGLE_QUOTED_TEXT.")?/As",
            'placeholder' => "/".preg_quote($this->placeholder)."([a-zA-Z_][a-zA-Z0-9_]*)/"
        ];
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * Gets the database utility.
     *
     * @return string
     */
    public function getUtility(): string|Utility
    {
        if (!$this->utility) {
            $this->utility = new Utility($this);
        }

        return $this->utility;
    }

    /**
     * Gets the table prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Replaces the table prefix placeholder with actual one.
     *
     * @param string $query
     * @return string
     */
    public function replacePrefix(string $query): string
    {
        $offset = 0;
        $length = strlen($this->prefix) - strlen($this->placeholder);

        foreach ($this->getUnquotedQueryParts($query) as $part) {

            if (!str_contains($part[0], $this->placeholder)) {
                continue;
            }

            $replace = preg_replace($this->regex['placeholder'], $this->prefix.'$1', $part[0], -1, $count);

            if ($count) {
                $query = substr_replace($query, $replace, $part[1] + $offset, strlen($part[0]));
                $offset += $length;
            }
        }

        return $query;
    }

    /**
     * Prepares and executes an SQL query and returns the first row of the result as an object.
     *
     * @param  string $statement
     * @param  array  $params
     * @param  string $class
     * @param  array  $args
     * @return mixed
     */
    public function fetchObject($statement, array $params = [], $class = 'stdClass', $args = [])
    {
        return $this->executeQuery($statement, $params)->fetchObject($class, $args);
    }

    /**
     * Prepares and executes an SQL query and returns the result as an array of objects.
     *
     * @param  string $statement
     * @param  array  $params
     * @param  string $class
     * @param  array  $args
     * @return array
     */
    public function fetchAllObjects($statement, array $params = [], $class = 'stdClass', $args = [])
    {
        return $this->executeQuery($statement, $params)->fetchAllAssociative(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class, $args);
    }

    /**
     * @{inheritdoc}
     */
    public function prepare($statement)
    {
        return parent::prepare($this->replacePrefix($statement));
    }

    /**
     * @{inheritdoc}
     */
    public function exec($statement)
    {
        return parent::executeStatement($this->replacePrefix($statement));
    }

    /**
     * @{inheritdoc}
     */
    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        return parent::executeQuery($this->replacePrefix($query), $params, $types, $qcp);
    }

    /**
     * @{inheritdoc}
     */
    public function executeUpdate($query, array $params = [], array $types = [])
    {
        return parent::executeStatement($this->replacePrefix($query), $params, $types);
    }

    /**
     * @{inheritdoc}
     * @param $table
     * @param array $data
     * @param array $types
     * @return int
     * @throws Exception
     */
    public function insert($table, array $data, array $types = [])
    {
        return parent::insert($this->replacePrefixTable($table), $data, $types);
    }

    /**
     * @{inheritdoc}
     * @param $table
     * @param array $criteria
     * @param array $types
     * @return int
     * @throws Exception
     */
    public function delete($table, array $criteria, array $types = [])
    {
        return parent::delete($this->replacePrefixTable($table), $criteria, $types);
    }

    /**
     * @{inheritdoc}
     * @param $table
     * @param array $data
     * @param array $criteria
     * @param array $types
     * @return int
     * @throws Exception
     */
    public function update($table, array $data, array $criteria, array $types = [])
    {
        return parent::update($this->replacePrefixTable($table), $data, $criteria, $types); // TODO: Change the autogenerated stub
    }

    /**
     * @{inheritdoc}
     */
    public function createQueryBuilder()
    {
        return new Query\QueryBuilder($this);
    }

    /**
     * Parses the unquoted SQL query parts.
     *
     * @param  string $query
     * @return array
     */
    protected function getUnquotedQueryParts($query)
    {
        preg_match_all($this->regex['quotes'], $query, $parts, PREG_OFFSET_CAPTURE);

        return $parts[1];
    }

    /**
     * @param $table
     * @return string|bool
     */
    protected function replacePrefixTable($table): string|bool
    {
        return str_replace($this->placeholder , $this->prefix , $table);
    }
}
