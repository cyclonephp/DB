<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class DB_Connector_Abstract implements DB_Connector {

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
