<?php

namespace cyclone\db;

/**
 * Interface for classes that are able to compile DB_Query_* query builder
 * objects to SQL queries for a given SQL dialect.
 *
 * Exactly one implementation belongs to each DBMS types and one instance to
 * each database adapters.
 * 
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 * @see DB::compiler()
 */
interface Compiler {

    public function compile_select(query\Select $query);

    public function compile_insert(query\Insert $query);

    public function compile_update(query\Update $query);

    public function compile_delete(query\Delete $query);
    
}
