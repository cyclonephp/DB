<?php

namespace cyclone\db\executor;

use cyclone\db;

/**
 * @package db
 * @author Bence Eros<crystal@cyclonephp.org>
 */
class PostgresExceptionBuilder {

    public static function for_error($err_str, $sql = '') {
        $rval = new db\ConstraintException($err_str);
        $rval->sql = $sql;
        $lines = explode(PHP_EOL, $err_str);
        $line = $lines[0];
        $exc_details = array();
        foreach ($lines as $line) {
            $sep_pos = strpos($line, ':');
            $exc_details[trim(substr($line, 0, $sep_pos))] = trim(substr($line, $sep_pos + 1));
        }

        $err_line = $exc_details['ERROR'];
        if (strpos($err_line, 'duplicate key value') === 0) {
            self::build_unique_exc($rval, $exc_details);
        } elseif (strpos($err_line, 'null value in column') === 0) {
            self::build_notnull_exc($rval, $exc_details);
        } elseif (strpos($err_line, 'insert or update on table') === 0) {
            self::build_foreignkey_exc($rval, $exc_details);
        } elseif (strpos($err_line, 'new row for relation ') === 0) {
            self::build_app_constraint_exc($rval, $exc_details);
        }
        return $rval;
    }

    private static function build_unique_exc(db\ConstraintException $ex, $exc_details) {
        $err_line = $exc_details['ERROR'];
        $ap_pos = strpos($err_line, '"');
        $ex->constraint_name = substr($err_line, $ap_pos + 1
                 , strlen($err_line) - $ap_pos - 2);
        $ex->constraint_type = db\ConstraintException::UNIQUE_CONSTRAINT;
        $detail_line = $exc_details['DETAIL'];
        $opening_bracket_pos = strpos($detail_line, '(');
        $ex->column = substr($detail_line, $opening_bracket_pos + 1,
                strpos($detail_line, ')') - $opening_bracket_pos - 1);

        $ex->detail = $detail_line;
        if (isset($exc_details['HINT'])) {
            $ex->hint = $exc_details['HINT'];
        }
    }

    private static function build_notnull_exc(db\ConstraintException $ex, $exc_details) {
        $ex->constraint_type = db\ConstraintException::NOTNULL_CONSTRAINT;
        $before_str_len = strlen('null value in column "');
        $err_line = $exc_details['ERROR'];
        $ex->column = substr($err_line, $before_str_len, strrpos($err_line, '"') - $before_str_len);
    }

    private static function build_foreignkey_exc(db\ConstraintException $ex, $exc_details) {
        $ex->constraint_type = db\ConstraintException::FOREIGNKEY_CONSTRAINT;
        $err_line = $exc_details['ERROR'];
        $ap_pos = strrpos($err_line, '"', -2);
        $ex->constraint_name = substr($err_line, $ap_pos + 1
                , strlen($err_line) - $ap_pos - 2);

        $detail_line = $exc_details['DETAIL'];
        $opening_bracket_pos = strpos($detail_line, '(');
        $ex->column = substr($detail_line, $opening_bracket_pos + 1,
                strpos($detail_line, ')') - $opening_bracket_pos - 1);
    }

    private static function build_app_constraint_exc(db\ConstraintException $ex, $exc_details) {
        $ex->constraint_type = db\ConstraintException::APP_CONSTRAINT;
        $err_line = $exc_details['ERROR'];
        $ap_pos = strrpos($err_line, '"', -2);
        $ex->constraint_name = substr($err_line, $ap_pos + 1
                , strlen($err_line) - $ap_pos - 2);
    }
    
}