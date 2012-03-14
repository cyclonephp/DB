<?php

namespace cyclone\db;
/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class BinaryExpression implements Expression {

    /**
     * The binary operator.
     *
     * @var string
     */
    public $operator;

    /**
     * The left operand of the expression. Can be a @c \cyclone\db\Expression instance
     * or a scalar value.
     *
     * @var mixed
     */
    public $left_operand;

    /**
     * The right operand of the expression. Can be a @c \cyclone\db\Expression instance
     * or a scalar value.
     *
     * @var mixed
     */
    public $right_operand;

    public function  __construct($left_operand, $operator, $right_operand) {
        $this->left_operand = $left_operand;
        $this->operator = $operator;
        $this->right_operand = $right_operand;
    }

    

    public function compile_expr(Compiler $adapter) {
        $left = ExpressionHelper::compile_operand($this->left_operand, $adapter);
        $right = ExpressionHelper::compile_operand($this->right_operand, $adapter);
        return $left.' '.$this->operator.' '.$right;
    }

    public function  contains_table_name($table_name) {
        $tbl_name_len = strlen($table_name);

        if (is_string($this->left_operand) 
                && substr($this->left_operand, 0, $tbl_name_len) == $table_name)
            return TRUE;

        if (is_string($this->right_operand)
                && substr($this->right_operand, 0, $tbl_name_len) == $table_name)
            return TRUE;

        if ($this->left_operand instanceof Expression
                && $this->left_operand->contains_table_name($table_name))
           return TRUE;

        if ($this->right_operand instanceof Expression
                && $this->right_operand->contains_table_name($table_name))
           return TRUE;

        return FALSE;
    }
}
