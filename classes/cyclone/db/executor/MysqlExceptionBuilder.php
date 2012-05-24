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

    const NOTNULL_CONSTRAINT = 1048;

    const UNIQUE_CONSTRAINT  = 1062;

    const FOREIGNKEY_CONSTRAINT = 1452;

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

        $builder = new db\ConstraintExceptionBuilder($err_msg . ' (' . $sql . ')');
        $builder->message = $err_msg;
        $builder->errcode = $errno;
        switch ($errno) {
            case self::NOTNULL_CONSTRAINT:
                $substr = substr($err_msg, strpos($err_msg, "'") + 1);
                $col_name = substr($substr, 0, strpos($substr, "'"));
                $builder->constraint_type = db\ConstraintException::NOTNULL_CONSTRAINT;
                $builder->column = $col_name;
                break;
            case self::UNIQUE_CONSTRAINT:
                $builder->constraint_type = db\ConstraintException::UNIQUE_CONSTRAINT;
                $parts = explode("'", $err_msg);
                $builder->column = $parts[3];;
                break;
            case self::FOREIGNKEY_CONSTRAINT:
                $parts = explode('`', $err_msg);
                $builder->constraint_name = $parts[5];
                $builder->column = $parts[7];
                $builder->constraint_type = db\ConstraintException::FOREIGNKEY_CONSTRAINT;
                break;
        }
        return $builder->build_exception();
    }

}
