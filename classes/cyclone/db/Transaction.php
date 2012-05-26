<?php

namespace cyclone\db;
use cyclone as cy;

/**
 * Represents a list of SQL queries that should be executed in a transaction.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Transaction extends \ArrayObject {

    /**
     * The queries to be executed during the transaction.
     *
     * @var array<\cyclone\db\Query>
     */
    protected $_queries;

    /**
     * @param array $queries the queries to be executed. Further queries added
     * with append() or offsetSet() will be appended to this array.
     */
    public function  __construct($queries = array()) {
        $this->_queries = $queries;
    }

    /**
     * Adds a database query for later execution of the transaction. The
     * query won't be executed immediately but when the @c exec() method
     * of the transaction is called.
     *
     * @param \cyclone\db\Query $value
     */
    public function append($value) {
        $this->_queries []= $value;
    }

    /**
     * Returns the number of queries in the transaction.
     *
     * @return int
     */
    public function  count() {
        return count($this->_queries);
    }

    public function  offsetExists($index) {
        return array_key_exists($index, $this->_queries);
    }

    public function  offsetGet($index) {
        return $this->_queries[$index];
    }

    public function  offsetSet($index, $newval) {
        $this->_queries[$index] = $newval;
    }

    public function  offsetUnset($index) {
        unset($this->_queries[$index]);
    }

    public function  getIterator() {
        return new ArrayIterator($this->_queries);
    }

    /**
     * Executes the transaction on the given database connection.
     *
     * Executes the queries in the same order as they were added to the
     * transaction. If any of the queries throw an exception then rolls
     * back the query then throws a new \cyclone\db\Exepction thats source is the original
     * exception.
     *
     * If all queries are successfully executed then commits the transaction.
     *
     * @param string $database
     * @throws \cyclone\db\Exception
     */
    public function exec($database = 'default') {
        $db = cy\DB::connector($database);
        $db->start_transaction();
        foreach ($this->_queries as $query) {
            try {
                $query->exec($database);
            } catch (\Exception $ex) {
                $db->rollback();
                throw new Exception('failed to execute transaction'
                        , $ex->getCode(), $ex);
            }
        }
        $db->commit();
    }

}
