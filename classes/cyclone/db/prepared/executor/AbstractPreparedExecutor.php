<?php

namespace cyclone\db\prepared\executor;

use cyclone\db;

/**
 * Abstract implementation of \cyclone\db\prepared\Executor.
 *
 * It contains only a constructor for dependency injection.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
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
     * @var resource
     */
    protected $_db_conn;

    public function  __construct($config, $db_conn) {
        $this->_config = $config;
        $this->_db_conn = $db_conn;
    }
    
}
