<?php

 namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class UnaryExpression implements Expression {

    /**
     * The prefix operator of the unary expression.
     *
     * @var string
     */
    public $operator;

    /**
     * The operand of the unary expression. Can be a @c \cyclone\db\Expression instance
     * or a scalar value.
     *
     * @var mixed
     */
    public $operand;

    public function  __construct($operator, $operand) {
        $this->operator = $operator;
        $this->operand = $operand;
    }

    public function  compile_expr(Compiler $adapter) {
        $op = $this->operand instanceof Expression ?
                $this->operand->compile_expr($adapter) : $this->operand;

        return $this->operator . ' ' . $op;
    }

    public function  contains_table_name($table_name) {
        if ($operand instanceof Expression)
            return $operand->contains_table_name($table_name);
        return FALSE;
    }
}
