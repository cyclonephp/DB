<?php

namespace cyclone\db\connector;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Mysqli extends AbstractConnector {

    protected $_in_transaction = FALSE;

    public function connect() {
        $conn = $this->_config['connection'];

        if (array_key_exists('persistent', $this->_config['connection'])
            && $conn['connection']) {
            $host = 'p:'.$conn['host'];
        } else {
            $host = $conn['host'];
        }

        $this->db_conn = new \mysqli($host, $conn['username'],
            $conn['password'], $conn['database']
            , \cyclone\Arr::get($conn, 'port',  ini_get('mysqli.default_port'))
            , \cyclone\Arr::get($conn, 'socket', ini_get('mysqli.default_socket')));
        if (mysqli_connect_errno())
            throw new db\ConnectionException('failed to connect: '.mysqli_connect_error());
        $this->db_conn->set_charset(isset($conn['charset']) ? $conn['charset'] : \cyclone\Env::$charset);
    }

    public function disconnect() {
        // safely disconnecting to avoid errors caused by double disconnects
        @$this->db_conn->close();
    }

    public function  start_transaction() {
        if ($this->_in_transaction)
            throw new db\Exception('MySQL connection "'
                . $this->_config['config_name']
                . '" is already in a transaction');

        if ( ! $this->db_conn->autocommit(FALSE))
            throw new db\Exception ('failed to change autocommit mode: ' . $this->db_conn->error);

        $this->_in_transaction = TRUE;
    }

    public function  commit() {
        if ( ! $this->_in_transaction)
            throw new db\Exception('Failed to commit. MySQL connection "'
                . $this->_config['config_name']
                . ' is not in a transaction');

        if ( ! $this->db_conn->commit())
            throw new db\Exception('failed to commit transaction: '
                    .$this->db_conn->error);

        if ( ! $this->db_conn->autocommit(TRUE))
            throw new db\Exception('failed to change autocommit mode: '  . $this->db_conn->error);

        $this->_in_transaction = FALSE;
    }

    public function rollback() {
        if ( ! $this->_in_transaction)
            throw new db\Exception('Failed to rollback. MySQL connection "'
                . $this->_config['config_name']
                . ' is not in a transaction');

        if ( ! $this->db_conn->rollback())
            throw new db\Exception('failed to rollback transaction: ' . $this->db_conn->error);

        if ( ! $this->db_conn->autocommit(TRUE))
            throw new db\Exception('failed to change autocommit mode: ' . $this->db_conn->error);

        $this->_in_transaction = FALSE;
    }
    
}
