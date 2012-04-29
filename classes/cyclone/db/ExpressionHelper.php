<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class ExpressionHelper {

    public static function compile_operand($operand, Compiler $adapter) {
        if ($operand instanceof Expression) {
            return $operand->compile_expr($adapter);
        } else {
            return $adapter->escape_identifier($operand);
        }
    }
}
