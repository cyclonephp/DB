<?php

namespace cyclone\db\executor;

use cyclone\db;
use cyclone\db\query;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Postgres extends AbstractExecutor {

    /**
     * The primary key generator sequences.
     *
     * @var array
     */
    private $_generator_sequences;

    public function  __construct($config, $db_conn) {
        parent::__construct($config, $db_conn);
        if (array_key_exists('pk_generator_sequences', $config)) {
            $this->_generator_sequences = $config['pk_generator_sequences'];
        } else {
            $this->_generator_sequences = array();
        }
    }

    public function exec_select($sql) {
        $result = @pg_query($this->_db_conn, $sql);
        if (FALSE === $result) {
            throw new db\Exception("Failed to execute SQL: " . pg_last_error($this->_db_conn)
            . '(query: ' . $sql . ')');
        }

        return new db\query\result\Postgres($result);
    }

    public function exec_insert($sql, query\Insert $orig_query = NULL) {
        if ( ($insert_result = @pg_query($this->_db_conn, $sql)) == FALSE)
            throw PostgresConstraintExceptionBuilder::for_error(pg_last_error($this->_db_conn), $sql);

        if ($orig_query !== NULL && count($orig_query->returning) > 0) {
            $rval = array();
            while ( ($row = pg_fetch_assoc($insert_result)) !== FALSE) {
                $rval []= $row;
            }
            return $rval;
        }
        return NULL;
    }

    public function exec_update($sql, query\Update $orig_query = NULL) {
        $result = @pg_query($this->_db_conn, $sql);
        if (FALSE == $result)
            throw new db\Exception('Failed to execute SQL: ' . pg_last_error($this->_db_conn)
                    . '(query: ' . $sql . ')');

        return pg_affected_rows($result);
    }

    public function exec_delete($sql, query\Delete $orig_query = NULL) {
        $result = @pg_query($this->_db_conn, $sql);
        if (FALSE == $result)
            throw new db\Exception('Failed to execute SQL: ' . pg_last_error($this->_db_conn)
                    . '(query: ' . $sql . ')');

        return pg_affected_rows($result);
    }

    public function exec_custom($sql) {
        try {
            return pg_query($this->_db_conn, $sql);
        } catch (\Exception $ex) {
            throw new db\Exception('Failed to execute SQL: ' . pg_last_error($this->_db_conn)
                    . '(query: ' . $sql . ')');
        }
    }
    
}
