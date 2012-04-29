<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
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
     * @var \cyclone\db\prepared\executor\AbstractPreparedExecutor
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
     * @return AbstractQuery $this
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
     * @return AbstractQuery $this
     */
    public function params(array $params) {
        $this->_params = $params;
        return $this;
    }

}
