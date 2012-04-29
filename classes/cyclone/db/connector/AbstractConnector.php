<?php

namespace cyclone\db\connector;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
abstract class AbstractConnector implements db\Connector {

    /**
     * The raw database connection.
     *
     * @var resource
     */
    public $db_conn;

    /**
     * @var array
     */
    protected $_config;

    public function  __construct($config) {
        $this->_config = $config;
        $this->connect();
    }

}
