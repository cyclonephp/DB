<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Expression {

    public function compile_expr(Compiler $adapter);

    /**
     * Returns TRUE if the expression contains the table $table_name,
     * otherwise FALSE.
     *
     * @param string $table_name
     * @return boolean
     */
    public function contains_table_name($table_name);
    
}
