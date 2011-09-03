<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class DB_Expression_Unary implements DB_Expression {

    /**
     * The prefix operator of the unary expression.
     *
     * @var string
     */
    public $operator;

    /**
     * The operand of the unary expression. Can be a \c DB_Expression instance
     * or a scalar value.
     *
     * @var mixed
     */
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
