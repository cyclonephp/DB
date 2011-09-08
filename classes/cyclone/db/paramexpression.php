<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class ParamExpression implements Expression {

    /**
     * An untrusted parameter that should be escaped.
     *
     * @var scalar
     */
    protected $val;

    public function  __construct($val) {
        $this->val = $val;
    }

    public function compile_expr(Compiler $adapter) {
        return $adapter->escape_param($this->val);
    }

    public function  contains_table_name($table_name) {
        return FALSE;
    }

}
