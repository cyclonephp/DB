<?php

namespace cyclone\db\executor;

use cyclone\db;
use cyclone\db\query;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Mysqli extends AbstractExecutor {

    public function  exec_select($sql) {
        $result = $this->_db_conn->query($sql);
        if ($result === false)
            throw new db\Exception($this->_db_conn->error . ' ( ' . $sql . ' )', $this->_db_conn->errno);
        return new db\query\result\Mysqli($result);
    }

    public function  exec_insert($sql, query\Insert $orig_query = NULL) {
        if ( ! $this->_db_conn->query($sql))
            throw new db\Exception($this->_db_conn->error, $this->_db_conn->errno);

        $returning_clause = $orig_query->returning;
        $affected_rows = $this->_db_conn->affected_rows;
        $rows = array();
        if (count($returning_clause) == 1) {
            $rows[0] = array($returning_clause[0] => $this->_db_conn->insert_id);
        } elseif (count($returning_clause) > 1) {
            $insert_id = $this->_db_conn->insert_id;
            $where_cond_column = array_shift($returning_clause);
            $query = cy\DB::select()->from($orig_query->table)
                ->where($where_cond_column, '=', cy\DB::esc($insert_id));
            $query->columns = $returning_clause;
            $compiler = cy\DB::compiler($this->_config['config_name']);
            $sql = $compiler->compile_select($query);
            $result = $this->exec_select($sql);
            if (count($result) == 0)
                throw new db\Exception('failed to retrieve inserted columns: "' . implode(', ', $orig_query->returning). '"');
            $result = $result->as_array();
            $result = $result[0];
            $result[$where_cond_column] = $insert_id;
            $rows[0] = $result;
        }
        return new db\StmtResult($rows, $affected_rows);
    }

    public function  exec_update($sql, query\Update $orig_query = NULL) {
        if ( ! $this->_db_conn->query($sql))
            throw new db\Exception($this->_db_conn->error, $this->_db_conn->errno);
        $rows = array();
        if ($orig_query !== NULL && count($orig_query->returning) > 0) {
            $query = new query\Select;
            $query->columns = $orig_query->returning;
            $query->where_conditions = $orig_query->conditions;
            $query->tables = array($orig_query->table);
            $sql = cy\DB::compiler($this->_config['config_name'])->compile_select($query);
            $result = $this->exec_select($sql);
            $rows = $result->as_array();
        }
        return new db\StmtResult($rows, $this->_db_conn->affected_rows);
    }

    public function  exec_delete($sql, query\Delete $orig_query = NULL) {
        if ( ! $this->_db_conn->query($sql))
            throw new db\Exception ($this->_db_conn->error, $this->_db_conn->errno);
        $rows = array();
        if ($orig_query !== NULL && count($orig_query->returning) > 0)
            throw new db\Exception('delete from returning is not yet implemented');

        return new db\StmtResult($rows, $this->_db_conn->affected_rows);
    }

    public function exec_custom($sql) {
        $result = $this->_db_conn->multi_query($sql);
        if ( ! $result)
            throw new db\Exception ('failed to execute query: '.$this->_db_conn->error
                    , $this->_db_conn->errno);
        $rval = array();
        do {
            $rval []= $this->_db_conn->store_result();
        } while ($this->_db_conn->more_results() && $this->_db_conn->next_result());
        return $rval;
    }
    
}
