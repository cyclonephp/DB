<?php

namespace cyclone\db\executor;

use cyclone\db;

/**
 * Helper class which is responsible for creating proper exception instances from
 * the error message of failed Postgres queries.
 *
 * The @c \cyclone\db\executor\Postgres and @c \cyclone\db\prepared\executor\Postgres
 * instances use the <code>PostgresExceptionBuilder</code> to create the exceptions to
 * be thrown on failed SQL queries and statements.
 *
 * @package db
 * @author Bence Eros<crystal@cyclonephp.org>
 */
class PostgresExceptionBuilder {

    /**
     * Creates the exception instance which should be thrown by the executor.
     *
     * The return value can be a @c \cyclone\db\ConstraintException or
     * a @c \cyclone\db\SchemaException instance.
     *
     * @param string $err_str the error message raised by the DBMS (obtained by
     *  the executors using <code>pg_last_error()</code>
     * @param string $sql the SQL command which failed
     * @return \cyclone\db\Exception
     */
    public static function for_error($err_str, $sql = '') {
        $rval = new db\ConstraintExceptionBuilder($err_str);
        $rval->sql = $sql;
        $lines = explode(PHP_EOL, $err_str);
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
        } elseif (strpos($err_line, 'relation "') === 0) {
            return self::build_schema_relation_exc($exc_details, $sql);
        } elseif (strpos($err_line, 'column "') === 0) {
            return self::build_schema_column_exc($exc_details, $sql);
        }  elseif (strpos($err_line, 'function ') === 0) {
            return self::build_schema_function_exc($exc_details, $sql);
        }
        return $rval->build_exception();
    }

    private static function build_unique_exc(db\ConstraintExceptionBuilder $ex, $exc_details) {
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

    private static function build_notnull_exc(db\ConstraintExceptionBuilder $ex, $exc_details) {
        $ex->constraint_type = db\ConstraintException::NOTNULL_CONSTRAINT;
        $before_str_len = strlen('null value in column "');
        $err_line = $exc_details['ERROR'];
        $ex->column = substr($err_line, $before_str_len, strrpos($err_line, '"') - $before_str_len);
    }

    private static function build_foreignkey_exc(db\ConstraintExceptionBuilder $ex, $exc_details) {
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

    private static function build_app_constraint_exc(db\ConstraintExceptionBuilder $ex, $exc_details) {
        $ex->constraint_type = db\ConstraintException::APP_CONSTRAINT;
        $err_line = $exc_details['ERROR'];
        $ap_pos = strrpos($err_line, '"', -2);
        $ex->constraint_name = substr($err_line, $ap_pos + 1
                , strlen($err_line) - $ap_pos - 2);
    }

    /**
     * Extracts the name of the missing relation and creates an exception
     * instance containing it.
     *
     * @param $exc_details array assoc. array, containing error message lines
     * @param $sql string the SQL command which raised the exception
     * @usedby for_error()
     * @return \cyclone\db\SchemaRelationException
     */
    private static function build_schema_relation_exc($exc_details, $sql) {
        $err_line = $exc_details['ERROR'];
        $ap_pos_first = strpos($err_line, '"');
        $ap_pos_last = strrpos($err_line, '"');
        $relation = substr($err_line, $ap_pos_first + 1, $ap_pos_last - $ap_pos_first - 1);
        return new db\SchemaRelationException($sql, 0, $relation);
    }

    /**
     * Extracts the relation name and the name of the missing column from
     * the error message string and created an exception instance containing
     * them.
     *
     * @param $exc_details array assoc. array containing error message lines
     * @param $sql string the SQL command which raised the exception
     * @usedby for_error()
     * @return \cyclone\db\SchemaColumnException
     */
    private static function build_schema_column_exc($exc_details, $sql) {
        $err_line = $exc_details['ERROR'];
        $parts = explode('"', $err_line);
        $relation = $parts[3];
        $column = $parts[1];
        return new db\SchemaColumnException($sql, 0, $relation, $column);
    }

    /**
     * Exctracts the name of the missing SQL function and creates an
     * exception instance containing it.
     *
     * @param $exc_details array assoc. array, containing error message lines
     * @param $sql string the SQL command which raised the exception
     * @usedby for_error()
     * @return \cyclone\db\SchemaFunctionException
     */
    private static function build_schema_function_exc($exc_details, $sql) {
        $err_line = $exc_details['ERROR'];
        $fn_literal_len = strlen('function ');
        $function = substr($err_line, $fn_literal_len, strpos($err_line, '(') - $fn_literal_len);
        return new db\SchemaFunctionException($sql, 0, $function);
    }
}