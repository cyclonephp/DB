<?php

namespace cyclone\db;
/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class CustomExpression implements Expression {

    /**
     * The custom database expression. No escaping or any other processing
     * will be made on this string while the query is compiled.
     *
     * @var string
     */
    public $str;

    public function  __construct($str) {
        $this->str = $str;
    }

    public function  compile_expr(Compiler $adapter) {
        return $this->str;
    }

    public function  contains_table_name($table_name) {
        return FALSE;
    }

}
