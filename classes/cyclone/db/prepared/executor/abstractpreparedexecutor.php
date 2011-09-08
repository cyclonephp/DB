<?php

namespace cyclone\db\prepared\executor;

use cyclone\db;

/**
 * Abstract implementation of DB_Executor_Prepared.
 *
 * It contains only a constructor for dependency injection.
 *
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class AbstractPreparedExecutor implements db\prepared\Executor {

    /**
     * @var array
     */
    protected $_config;

    /**
     * The raw database connection.
     *
     * @var recource
     */
    protected $_db_conn;

    public function  __construct($config, $db_conn) {
        $this->_config = $config;
        $this->_db_conn = $db_conn;
    }
    
}
