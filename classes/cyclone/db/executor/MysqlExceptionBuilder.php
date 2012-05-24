<?php

namespace cyclone\db\executor;

use cyclone as cy;
use cyclone\db;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class MysqlExceptionBuilder {

    const MISSING_RELATION = 1146;

    const MISSING_COLUMN = 1054;

    const MISSING_FUNCTION = 1305;

    public static function for_error($err_msg, $errno, $sql = '') {
        switch ($errno) {
            case self::MISSING_RELATION:
                $substr = substr($err_msg, strpos($err_msg, '.') + 1);
                $rel_name = substr($substr, 0, strpos($substr, "'"));
                return new db\SchemaRelationException($err_msg, $errno, $rel_name);
            case self::MISSING_COLUMN:
                $substr = substr($err_msg, strpos($err_msg, "'") + 1);
                $col_name = substr($substr, 0, strpos($substr, "'"));
                return new db\SchemaColumnException($err_msg, $errno, NULL, $col_name);
            case self::MISSING_FUNCTION:
                $substr = substr($err_msg, strpos($err_msg, ".") + 1);
                $fn_name = substr($substr, 0, strpos($substr, ' '));
                return new db\SchemaFunctionException($err_msg, $errno, $fn_name);
        }
    }
}
