<?php

namespace cyclone\db\executor;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class AbstractExecutor implements db\Executor {

    /**
     * The configuration passed in the constructor.
     *
     * @var array
     */
    protected $_config;

    /**
     * The raw database connection.
     *
     * @var mixed
     */
    protected $_db_conn;

    public function  __construct($config, $db_conn) {
        $this->_config = $config;
        $this->_db_conn = $db_conn;
    }
    
}
