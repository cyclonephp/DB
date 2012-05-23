<?php


namespace cyclone\db\prepared\executor;

use cyclone\db;
use cyclone\db\executor;

/**
 * Implementation of \cyclone\db\prepared\Executor for MySQLi
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Mysqli extends AbstractPreparedExecutor {

    /**
     * Creates an prepared statement from the given SQL query
     *
     * @param string $sql
     * @return MySQLi_Stmt
     * @throws \cyclone\db\Exception if the preparation fails.
     */
    public function prepare($sql) {
        $rval = $this->_db_conn->prepare($sql);
        if (FALSE === $rval) 
            throw new db\Exception("'failed to prepare statement: '$sql' Cause: {$this->_db_conn->error}", $this->_db_conn->errno);

        return $rval;
    }

    private function add_type_string(array &$params) {
        $type_str = '';
        foreach ($params as $k => $param) {
            switch (gettype($param)) {
                case 'integer' :
                    $type_str .= 'i';
                    break;
                case 'float' :
                    $type_str .= 'd';
                    break;
                case 'string' :
                    $type_str .= 's';
                    break;
                case 'boolean' :
                    $type_str .= 'i';
                    // converting booleans to 0 / 1
                    $param = $param ? '1' : '0';
                    break;
                default:
                    if (is_array($param))
                        throw new db\Exception('prepared statement parameters cannot be arrays');
                    // converting objects to their string representation
                    if (is_object($param)) {
                        $type_str .= 's';
                        $param = (string) $param;
                    }
            }
        }
        array_unshift($params, $type_str);
    }

    /**
     *
     * @param MySQLi_Stmt $prepared_stmt
     * @param array $params
     */
    public function exec_select($prepared_stmt, array $params
            , db\query\Select $orig_query) {
        if ( ! empty($params)) {
            $this->add_type_string($params);
            // I know that this foreach seems to be totally useless..
            // but please don't try to remove it, you will find yourself in a
            // fuckin' big shit of PHP
            $tmp = array();
            foreach ($params as $k => $p) $tmp [$k]= &$params[$k];
            call_user_func_array(array($prepared_stmt, 'bind_param'), $tmp);
        }
        $prepared_stmt->execute();
        $prepared_stmt->store_result();
        return new db\prepared\result\MySQLi($prepared_stmt, $orig_query);
    }

    public function exec_insert($prepared_stmt, array $params
            , db\query\Insert $orig_query) {
        if ( ! empty($params)) {
            $this->add_type_string($params);
            $tmp = array();
            foreach ($params as $k => $p) $tmp [$k]= &$params[$k];
            call_user_func_array(array($prepared_stmt, 'bind_params'), $params);
        }
        $prepared_stmt->execute();
        return executor\Mysqli::stmt_result_for_insert($orig_query
            , $this->_config['config_name']
            , $this->_db_conn->affected_rows
            , $this->_db_conn->insert_id);
    }

    public function exec_update($prepared_stmt, array $params
            , db\query\Update $orig_query) {
        if ( ! empty($params)) {
            $this->add_type_string($params);
            $tmp = array();
            foreach ($params as $k => $p) $tmp [$k]= &$params[$k];
            call_user_func_array(array($prepared_stmt, 'bind_params'), $params);
        }
        $prepared_stmt->execute();
        return executor\Mysqli::stmt_result_for_update($orig_query
            , $this->_config['config_name']
            , $this->_db_conn->affected_rows);
    }

    public function exec_delete($prepared_stmt, array $params
            , db\query\Delete $orig_query) {
        if ( ! empty($params)) {
            $this->add_type_string($params);
            $tmp = array();
            foreach ($params as $k => $p) $tmp [$k]= &$params[$k];
            call_user_func_array(array($prepared_stmt, 'bind_params'), $params);
        }
        $prepared_stmt->execute();
        return $this->_db_conn->affected_rows;
    }
    
}
