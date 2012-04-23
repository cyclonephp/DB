<?php

namespace cyclone\db\compiler;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
abstract class AbstractCompiler implements db\Compiler {

    /**
     * The configuration passed in the constructor
     *
     * @var array
     */
    protected $_config;

    /**
     * The database connection.
     *
     * @var resource
     */
    protected $_db_conn;

    /**
     * Sequence of table aliases used in the query. This array is populated
     * bye the select_aliases() method and used by escape_identifier() to avoid
     * appending the table prefix (optionally set up for the connection) to table
     * aliases.
     *
     * @var array
     */
    protected $_table_aliases = array();

    public function  __construct($config, $db_conn) {
        $this->_config = $config;
        $this->_db_conn = $db_conn;
    }

    /**
     * Compiles a qcyclone\db\query\Select instance to SQL according to the SQL
     * dialect of the DBMS. Recommended to use \cyclone\db\query\Select::compile() instead.
     *
     * @param \cyclone\db\query\Select $query
     * @return string the generated SQL
     * @usedby \cyclone\db\query\Select::compile()
     */
    public function  compile_select(db\query\Select $query) {
        $this->select_aliases($query->tables, $query->joins);
        $rval = 'SELECT ';
        if ($query->distinct) {
            $rval .= 'DISTINCT ';
        }
        $rval .= $this->escape_values($query->columns);
        $rval .= ' FROM ';
        $tbl_names = array();
        foreach ($query->tables as $table) {
            $tbl_names []= $this->escape_table($table);
        }
        $rval .= implode(', ', $tbl_names);
        if ( ! empty($query->hints)){
            $rval .= $this->compile_hints($query->hints);
        }
        if ( ! empty($query->joins)) {
            foreach ($query->joins as $join) {
                $rval .= ' ' . $join['type'] . ' JOIN ' . $this->escape_table($join['table']);
                $rval .= ' ON ' . $this->compile_expressions($join['conditions']);
            }
        }
        if ( ! empty($query->where_conditions)) {
            $rval .= ' WHERE '.$this->compile_expressions($query->where_conditions);
        }
        if ( ! empty($query->group_by)) {
            $rval .= ' GROUP BY '.$this->escape_values($query->group_by);
        }
        if ( ! empty($query->having_conditions)) {
            $rval .= ' HAVING '.$this->compile_expressions($query->having_conditions);
        }
        if ( ! empty($query->order_by)) {
            $rval .= ' ORDER BY ';
            foreach ($query->order_by as $ord) {
                $rval .= $this->escape_value($ord['column']).' '.$ord['direction'];
            }
        }
        if ( ! is_null($query->limit)) {
            $rval .= ' LIMIT '.$query->limit;
        }
        if ( ! is_null($query->offset)) {
            $rval .= ' OFFSET '.$query->offset;
        }
        if ( ! empty($query->unions)) {
            foreach($query->unions as $union) {
                $rval .= ' UNION ';
                if ($union['all'] == TRUE) {
                    $rval .= 'ALL ';
                }
                $rval .= $this->compile_select($union['select']);
            }
        }
        return $rval;
    }



    /**
     * Compiles a \cyclone\db\query\Insert instance to SQL according to the SQL
     * dialect of the DBMS. Recommended to use \cyclone\db\query\Insert::compile() instead.
     *
     * @param \cyclone\db\query\Insert $query
     * @return string the generated SQL
     * @usedby \cyclone\db\query\Insert::compile()
     */
    public function  compile_insert(db\query\Insert $query) {
        $this->select_aliases($query->table);
        $rval = 'INSERT INTO ';
        $rval .= $this->escape_table($query->table);
        if (empty($query->values))
            throw new db\Exception('no value lists to be inserted');
        $rval .= ' ('.$this->escape_values(array_keys($query->values[0])).') VALUES ';
        foreach ($query->values as $value_set) {
            $value_sets []= '('.$this->escape_params($value_set).')';
        }

        $rval .= implode(', ', $value_sets);
        return $rval;
    }

    /**
     * Compiles a \cyclone\db\query\Update instance to SQL according to the SQL
     * dialect of the DBMS. Recommended to use \cyclone\db\query\Update::compile() instead.
     *
     * @param \cyclone\db\query\Update $query
     * @return string the generated SQL
     * @usedby \cyclone\db\query\Update::compile()
     */
    public function  compile_update(db\query\Update $query) {
        $this->select_aliases($query->table);
        $rval = 'UPDATE ';
        $rval .= $this->escape_table($query->table);
        $rval .= ' SET ';
        foreach ($query->values as $k => $v) {
            $pieces []= $this->escape_identifier($k).' = '.$this->escape_param($v);
        }
        $rval .= implode(', ', $pieces);
        if ( ! empty($query->conditions)) {
            $rval .= ' WHERE '.$this->compile_expressions($query->conditions);
        }
        if ( ! is_null($query->limit)) {
            $rval .= ' LIMIT '.$query->limit;
        }
        return $rval;
    }

    /**
     * Compiles a \cyclone\db\query\Delete to SQL according to the SQL dialect
     * of the DBMS. Recommended to use \cyclone\db\query\Delete::compile() instead.
     *
     * @param \cyclone\query\Delete $query
     * @return string the generated SQL
     * @usedby \cyclone\db\uery\Delete::compile()
     */
    public function compile_delete(db\query\Delete $query) {
        $this->select_aliases($query->table);
        $rval = 'DELETE FROM ';
        $rval .= $this->escape_table($query->table);
        if ( ! empty($query->conditions)) {
            $rval .= ' WHERE '.$this->compile_expressions($query->conditions);
        }
        if ( ! is_null($query->limit)) {
            $rval .= ' LIMIT '.$query->limit;
        }
        return $rval;
    }

    protected abstract function compile_alias($expr, $alias);

    protected abstract function compile_hints($hints);

     protected function escape_values($columns) {
        foreach ($columns as $column) {
            if (is_array($column)) {
                $expr = $column[0];
                $alias = $column[1];
                if ($expr instanceof db\Expression) {
                    $expr = '('.$expr->compile_expr($this).')';
                } else {
                    $expr = $this->escape_identifier($expr);
                }
                $escaped_cols []= $this->compile_alias($expr, $alias);
            } else {
                if ($column instanceof db\Expression) {
                    $escaped_cols []= $column->compile_expr($this);
                } else {
                    $escaped_cols []= $this->escape_identifier($column);
                }
            }
        }
        return implode(', ', $escaped_cols);
    }

    protected function escape_value($val) {
        if (is_array($val)) {
            $expr = $val[0];
            $alias = $val[1];
            if ($expr instanceof db\Expression) {
                $expr = '(' . $expr->compile_expr($this) . ')';
            } else {
                $expr = $this->escape_identifier($expr);
            }
            return $this->compile_alias($expr, $alias);
        } else {
            if ($val instanceof db\Expression) {
                return $val->compile_expr($this);
            } else {
                return $this->escape_identifier($val);
            }
        }
    }

    protected function escape_params($params) {
        foreach ($params as $param) {
            $escaped_params []= $this->escape_param($param);
        }
        return implode(', ', $escaped_params);
    }

    /**
     * @param string $identifier database table or column name
     * @access package
     */
    public function escape_identifier($identifier) {
        if ($identifier instanceof db\Expression)
            return $identifier->compile_expr($this);

        $esc_char = $this->esc_char;

        $segments = explode('.', $identifier);
        $rval = $esc_char . $segments[0] . $esc_char;
        if(isset($this->_config['prefix']) && count($segments) == 2){
            if( ! in_array($segments[0], $this->_table_aliases)){
                $rval =  $esc_char . $this->_config['prefix'].$segments[0] . $esc_char;
            }
        }

        if (count($segments) > 1) {
            if ('*' == $segments[1]) {
                $rval .= '.*';
            } else {
                $rval .= '.' . $esc_char . $segments[1] . $esc_char;
            }
        }
        return $rval;
    }

    /**
     * This method is responsible for prventing SQL injection.
     *
     * @param string $param user parameter that should be escaped
     */
    protected abstract function escape_param($param);

    protected abstract function escape_table($table);

    protected function compile_expressions($expr_list) {
        $compiled_exprs = array();
        foreach ($expr_list as $expr) {
            $compiled_exprs []= $expr->compile_expr($this);
        }
        return implode(' AND ', $compiled_exprs);
    }

    protected function select_aliases($tables, $joins = NULL){
        if(is_array($tables)){
            foreach($tables as $table){
                if(is_array($table)){
                    $this->_table_aliases[] = $table[1];
                }
            }
        }
        if (is_array($joins)){
            foreach($joins as $join){
                if(is_array($join['table'])){
                    $this->_table_aliases[] = $join['table'][1];
                }
            }
        }
    }
    
}
