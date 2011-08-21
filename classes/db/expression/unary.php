<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class DB_Expression_Unary implements DB_Expression {

    public $operator;

    public $operand;

    public function  __construct($operator, $operand) {
        $this->operator = $operator;
        $this->operand = $operand;
    }

    public function  compile_expr(DB_Compiler $adapter) {
        $op = $this->operand instanceof DB_Expression ?
                $this->operand->compile_expr($adapter) : $this->operand;

        return $this->operator . ' ' . $op;
    }

    public function  contains_table_name($table_name) {
        if ($operand instanceof DB_Expression)
            return $operand->contains_table_name($table_name);
        return FALSE;
    }
}
