<?php

namespace cyclone\db\connector;

use cyclone as cy;
use cyclone\db;
use cyclone\db\executor;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Postgres extends AbstractConnector {

    protected $_in_transaction = FALSE;

    public function connect() {
        $conn_params = array();
        if (array_key_exists('persistent', $this->_config['connection'])) {
            $persistent = $this->_config['connection']['persistent'];
            unset($this->_config['connection']['persistent']);
        } else {
            $persistent = FALSE;
        }
        $valid_options = array('user', 'password', 'host', 'port', 'dbname');
        foreach ($this->_config['connection'] as $k => $v) {
            if (in_array($k, $valid_options)) {
                $conn_params []= "$k=$v";
            }
        }
        $conn_str = implode(' ', $conn_params);
        //die("before connect $conn_str\n");
        if ($persistent) {
            $this->db_conn = @pg_pconnect($conn_str);
        } else {
            $this->db_conn = @pg_connect($conn_str);
        }
        
        if (FALSE == $this->db_conn)
            throw new db\ConnectionException('failed to connect to database: '.$conn_str);

        if (array_key_exists('pk_generator_sequences', $this->_config)) {
            $this->_generator_sequences = $this->_config['pk_generator_sequences'];
        } else {
            $this->_generator_sequences = array();
        }
    }

    public function  disconnect() {
        if ( ! pg_close($this->db_conn))
            throw new db\Exception("failed to disconnect from database '{$this->_config['connection']['dbname']}'");
    }

    public function start_transaction() {
        if ($this->_in_transaction)
            throw new db\Exception('postgres connection "'
                . $this->_config['config_name']
                . ' is already in a transaction');

        cy\DB::executor($this->_config['config_name'])->exec_custom('BEGIN WORK');
        $this->_in_transaction = TRUE;
    }

    public function commit() {
        if ( ! $this->_in_transaction)
            throw new db\Exception('Failed to commit. Postgres connection "'
                . $this->_config['config_name']
                . ' is not in a transaction');

        cy\DB::executor($this->_config['config_name'])->exec_custom('COMMIT');
        $this->_in_transaction = FALSE;
    }

    public function rollback() {
        if ( ! $this->_in_transaction)
            throw new db\Exception('Failed to rollback. Postgres connection "'
                . $this->_config['config_name']
                . ' is not in a transaction');

        cy\DB::executor($this->_config['config_name'])->exec_custom('ROLLBACK');
        $this->_in_transaction = FALSE;
    }
    
}
