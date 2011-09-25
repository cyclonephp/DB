<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * Abstract implementation of DB_Query_Prepared. Doesn't implement
 * DB_Query_Prepared::exec();
 *
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class AbstractQuery implements db\prepared\Query {

    /**
     * The parameters of the prepared statement
     *
     * @var array
     */
    protected $_params = array();

    /**
     * The raw SQL
     *
     * @var string
     */
    protected $_sql;


    /**
     * @var mixed
     */
    protected $_prepared_stmt;

    /**
     * @var DB_Executor_Prepared
     */
    protected  $_executor;

    public function  __construct($sql, $database) {
        $this->_sql = $sql;
        $this->_executor = \cyclone\DB::executor_prepared($database);
        $this->_prepared_stmt = $this->_executor->prepare($sql, $database);
    }

    /**
     * @param string $key
     * @param scalar $value
     * @return DB_Query_Prepared_Abstract
     */
    public function param($value, $key = '?') {
        if ('?' == $key) {
            $this->_params []= &$value;
            return $this;
        }
        $this->_params[$key] = &$value;
        return $this;
    }

    /**
     * @param array $params
     * @return DB_Query_Prepared_Abstract
     */
    public function params(array $params) {
        $this->_params = $params;
        return $this;
    }

}
