<?php

namespace cyclone\db\compiler;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Postgres extends AbstractCompiler {

    /**
     * The table or column name escape character for Postgres is <code>"</code>.
     *
     * @var string
     */
    protected $esc_char = '"';

    public function  compile_hints($hints) {
        throw new db\Exception("postgres doesn't support hints");
    }

    public function compile_alias($expr, $alias) {
        return $expr.' AS "'.$alias.'"';
    }

    /**
     * This method is responsible for prventing SQL injection.
     *
     * @param string $param user parameter that should be escaped
     */
    public function escape_param($param) {
        if (NULL === $param)
            return 'NULL';

        return "'" . pg_escape_string($this->_db_conn, $param) . "'";
    }

    public function escape_table($table) {
        if ($table instanceof DB_Expression)
            return $table->compile_expr($this);

        $prefix = isset($this->_config['prefix'])
                ? $this->_config['prefix']
                : '';

        if (is_array($table)) {
            list($table_name, $alias) = $table;
            if ($table_name instanceof db\Expression) {
                $rtable = '(' . $table_name->compile_expr($this) . ') "' . $table[1] . '"';
            } else {
                $rtable = '"' . $prefix . $table[0] . '" "' . $table[1] . '"';
            }
        } else {
            $rtable = '"' . $prefix . $table . '"';
        }

        return $rtable;
    }
    
}
