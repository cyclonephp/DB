<?php

namespace cyclone\db;

/**
 * Interface for classes that are able to compile query builder objects in
 * the \cyclone\db\query namespace to SQL queries for a given SQL dialect.
 *
 * The instances of the classes of the <code>\cyclone\db\query</code> namespace
 * are intended to represent SQL queries and statements in a DBMS-independent way.
 * The task of the <code>Compiler</code> implementations is to create a
 * database-specific SQL query string from query objects.
 *
 * Exactly one implementation belongs to each DBMS types and one instance to
 * each database adapters.
 * 
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 * @see cyclone\DB::compiler()
 */
interface Compiler {

    public function compile_select(query\Select $query);

    public function compile_insert(query\Insert $query);

    public function compile_update(query\Update $query);

    public function compile_delete(query\Delete $query);
    
}
