<?php

namespace cyclone\db\executor;

use cyclone\db;

class PostgresConstraintExceptionBuilder {

    public static function for_error($err_str) {
        var_dump($err_str);
        $rval = new db\ConstraintException($err_str);
        $lines = explode(PHP_EOL, $err_str);
        $line = $lines[0];
        $exc_details = array();
        foreach ($lines as $line) {
            $sep_pos = strpos($line, ':');
            $exc_details[trim(substr($line, 0, $sep_pos))] = trim(substr($line, $sep_pos + 1));
        }

        if (strpos($exc_details['ERROR'], 'duplicate key value') === 0) {
            self::build_unique_exc($rval, $exc_details);
        } elseif (strpos($exc_details['ERROR'], 'null value in column') === 0) {
            self::build_notnull_exc($rval, $exc_details);
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
    
}