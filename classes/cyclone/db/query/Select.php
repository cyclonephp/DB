<?php

namespace cyclone\db\query;
use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Select implements db\Query, db\Expression {

    /**
     * If <code>TRUE</code> then the query will be compiled as a
     * <code>SELECT DISTINCT</code> query.
     *
     * @var boolean
     */
    public $distinct;

    /**
     * Sequence of columns to be selected. Every item of the array can be:
     * <ul>
     * <li>a string (column name)</li>
     * <li>an array that's 0th item is a column name, the 1st is a column alias</li>
     * <li>an array that's 0th item is a @c \cyclone\db\Expression instance, the 1st item
     *      is an alias</li>
     * </ul>
     *
     * @var array
     */
    public $columns;

    /**
     * @var array
     */
    public $tables;

    /**
     * @var array
     */
    public $joins = NULL;

    /**
     * @var array
     */
    protected $_last_join;

    /**
     * @var array<\cyclone\db\Expression>
     */
    public $where_conditions;

    /**
     * @var string
     */
    public $group_by;

    /**
     * @var array
     */
    public $having_conditions;

    /**
     * @var string
     */
    public $order_by;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var boolean
     */
    public $for_update;

    /**
     * @var array<\cyclone\db\query\Select>
     */
    public $unions = array();

    /**
     * @var array<string>
     */
    public $hints = array();

    public function columns() {
        $args = func_get_args();
        $this->columns_arr($args);
        return $this;
    }

    public function columns_arr($columns) {
        if (empty($columns)) {
            $this->columns = array(cy\DB::expr('*'));
        } else {
            foreach ($columns as $col) {
                $this->columns []= $col;
            }
        }
        return $this;
    }

    public function from($table) {
        $this->tables []= $table;
        return $this;
    }

    public function join($table, $join_type = 'INNER') {
        $join = array(
            'table' => $table,
            'type' => $join_type,
            'conditions' => array()
        );
        $this->joins []= &$join;
        $this->_last_join = &$join;
        return $this;
    }

    public function left_join($table) {
        return $this->join($table, 'LEFT');
    }

    public function right_join($table) {
        return $this->join($table, 'RIGHT');
    }

    public function on() {
        $this->_last_join['conditions'] []= cy\DB::create_expr(func_get_args());
        return $this;
    }

    public function where() {
        $this->where_conditions []= cy\DB::create_expr(func_get_args());
        return $this;
    }

    public function order_by($column, $direction = 'ASC') {
        $this->order_by []= array(
            'column' => $column,
            'direction' => $direction
        );
        return $this;
    }

    public function group_by() {
        $this->group_by = func_get_args();
        return $this;
    }

    public function having() {
        $this->having_conditions []= cy\DB::create_expr(func_get_args());
        return $this;
    }

    public function offset($offset) {
        $this->offset = (int) $offset;
        return $this;
    }

    public function limit($limit) {
        $this->limit = (int) $limit;
        return $this;
    }

    public function for_update() {
        $this->for_update = true;
        return $this;
    }

    public function compile($database = 'default') {
        return cy\DB::compiler($database)->compile_select($this);
    }

    /**
     *
     * @param string $database
     * @return \cyclone\db\query\result\AbstractResult
     */
    public function exec($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_select($this);
        return cy\DB::executor($database)->exec_select($sql);
    }

    public function  prepare($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_select($this);
        return new db\prepared\query\Select($sql, $database, $this);
    }

    public function  compile_expr(db\Compiler $adapter) {
        return '(' . $adapter->compile_select($this) . ')';
    }

    public function  contains_table_name($table_name) {
        $tbl_name_len = strlen($table_name);
        foreach ($this->tables as $tbl) {
            if (is_array($tbl)) {
                $tbl = $tbl[0];
            }
            // if it's a string, then check if it starts with the table name
            if (is_string($tbl) && substr($tbl, 0, $tbl_name_len) == $table_name)
                return TRUE;

            // if it's not a string then it must be a @c Select instance
            if ($tbl->contains_table_name($table_name))
                return TRUE;
        }
        foreach ($this->joins as $join) {
            if (is_array($join['table'])) {
                $join_tbl = $join['table'][0];
            } else {
                $join_tbl = $join['table'];
            }
            if (is_string($join_tbl)
                    && substr($join_tbl, 0, $tbl_name_len) == $table_name)
                return TRUE;
            if ($join_tbl->contains_table_name($table_name))
                return TRUE;

            // for joins, we also have to check join conditions
            foreach($join['conditions'] as $join_cond) {
                if ($join_cond->contains_table_name($table_name))
                    return TRUE;
            }
        }
        return FALSE;
    }

    public function union(db\query\Select $select, $all = TRUE){
        $this->unions[] = array(
            'select' => $select,
            'all' => $all
        );
        return $this;
    }

    public function hint($hint){
        $this->hints[] = $hint;
        return $this;
    }

    /**
     * Checks if <code>$this</code> is the same as <code>$other</code>. Returns <code>TRUE</code> on
     * success, <code>FALSE</code> on failure. The comparison is done with omitting the internal
     * property holding a reference to the last join, every other properties of <code>$this</code>
     * and <code>$other</code> are compared.
     *
     * @param Select $other the other select query to compare against
     * @return bool
     */
    public function equals(Select $other) {
        $rval = ($this->distinct == $other->distinct)
            && ($this->columns == $other->columns)
            && ($this->tables == $other->tables)
            && ($this->joins == $other->joins)
            && ($this->where_conditions == $other->where_conditions)
            && ($this->group_by == $other->group_by)
            && ($this->having_conditions == $other->having_conditions)
            && ($this->order_by == $other->order_by)
            && ($this->offset == $other->offset)
            && ($this->limit == $other->limit)
            && ($this->for_update == $other->for_update)
            && ($this->hints == $other->hints)
            ;
        if ($rval) {
            if (count($this->unions) !== count($other->unions))
                return FALSE;

            foreach ($this->unions as $k => $union) {
                if ( ! isset($other->unions[$k])) {
                    return FALSE;
                }

                $other_union = $other->unions[$k];
                if ( ! (array_key_exists('select', $other_union) && array_key_exists('all', $other_union)))
                    return FALSE;

                if ($union['all'] !== $other_union[$k]['all'])
                    return FALSE;

                if ( ! ($union['select'] instanceof Select) || ! ($other_union['select'] instanceof Select))
                    return FALSE;

                if ( ! $union['select']->equals($other_union['select']))
                    return FALSE;
            }
        }
        return $rval;
    }
}
