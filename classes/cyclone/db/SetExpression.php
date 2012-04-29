<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class SetExpression implements Expression {

    /**
     * The set to be escaped.
     *
     * @var array
     */
    protected $arr;

    public function  __construct($arr) {
        $this->arr = $arr;
    }


    public function  compile_expr(Compiler $adapter) {
        $escaped_items = array();
        foreach ($this->arr as $itm) {
            $escaped_items []= $adapter->escape_param($itm);
        }
        return '('.implode(', ', $escaped_items).')';
    }

    public function  contains_table_name($table_name) {
        return FALSE;
    }
}
