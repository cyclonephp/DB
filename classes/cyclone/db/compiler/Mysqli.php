<?php


namespace cyclone\db\compiler;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Mysqli extends AbstractCompiler {

    /**
     * The table or column name escape character for MySQL is <code>`</code>.
     *
     * @var string
     */
    protected $esc_char = '`';

     /**
     * Compiles a \cyclone\db\query\Select instance to SQL according to the SQL
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
        $rval .= ' FROM (';
        $tbl_names = array();

        if (empty($query->tables))
            throw new db\Exception("failed to compile query: no tables found in FROM clause");
        foreach ($query->tables as $table) {
            $tbl_names []= $this->escape_table($table);
        }
        $rval .= implode(', ', $tbl_names) . ')';
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
            $order_by_itms = array();
            foreach ($query->order_by as $ord) {
                $order_by_itms []= $this->escape_value($ord['column']).' '.$ord['direction'];
            }
            $rval .= implode(', ', $order_by_itms);
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

    public function compile_hints($hints) {
        $rval = " USE";
        foreach ($hints as $hint) {
            $rval .= ' ' . $hint;
        };
        return $rval;
    }

    public function escape_table($table){
        if ($table instanceof db\Expression)
            return $table->compile_expr($this);

        $prefix = array_key_exists('prefix', $this->_config)
                ? $this->_config['prefix']
                : '';

        if (is_array($table)) {
            list($table_name, $alias) = $table;
            if ($table_name instanceof db\Expression) {
                $rtable = '(' . $table_name->compile_expr($this) . ') `' . $table[1] . '`';
            } else {
                $rtable = '`' . $prefix . $table[0] . '` `' . $table[1] . '`';
            }
        } else {
            $rtable = '`' . $prefix . $table . '`';
        }

        return $rtable;
    }

    public function escape_param($param) {
        if (NULL === $param)
            return 'NULL';

        return "'".$this->_db_conn->real_escape_string($param)."'"; //TODO test & secure
    }

    public function compile_alias($expr, $alias) {
        return $expr.' AS `'.$alias.'`';
    }
    
}
