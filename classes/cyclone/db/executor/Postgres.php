<?php

namespace cyclone\db\executor;

use cyclone\db;
use cyclone\db\query;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
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

    protected function create_stmt_result($stmt_result_resource, $returing_clause) {
        $affected_rows = pg_affected_rows($stmt_result_resource);
        $rows = array();
        if ($returing_clause !== NULL && count($returing_clause) > 0) {
            while ( ($row = pg_fetch_assoc($stmt_result_resource)) !== FALSE) {
                $rows []= $row;
            }
        }
        return new db\StmtResult($rows, $affected_rows);
    }

    public function exec_insert($sql, query\Insert $orig_query = NULL) {
        if ( ($insert_result = @pg_query($this->_db_conn, $sql)) == FALSE)
            throw PostgresConstraintExceptionBuilder::for_error(pg_last_error($this->_db_conn), $sql);

        return $this->create_stmt_result($insert_result, $orig_query === NULL ? NULL : $orig_query->returning);
    }

    public function exec_update($sql, query\Update $orig_query = NULL) {
        $result = @pg_query($this->_db_conn, $sql);
        if (FALSE == $result)
            throw PostgresConstraintExceptionBuilder::for_error(pg_last_error($this->_db_conn), $sql);

        return $this->create_stmt_result($result, $orig_query === NULL ? NULL : $orig_query->returning);
    }

    public function exec_delete($sql, query\Delete $orig_query = NULL) {
        $result = @pg_query($this->_db_conn, $sql);
        if (FALSE == $result)
            throw PostgresConstraintExceptionBuilder::for_error(pg_last_error($this->_db_conn), $sql);

        return $this->create_stmt_result($result, $orig_query === NULL ? NULL : $orig_query->returning);
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
