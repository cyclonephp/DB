<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * An implementing class should exist for each DBMS supported by CyclonePHP.
 *
 * Implementations are able to build proper DDL strings from the PHP objects that
 * represent database objects. These PHP objects are DB_Schema_Table and
 * DB_Schema_Column.
 * 
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
interface Generator {

    /**
     * Generates the database-specific DDL command for the table.
     *
     * @param DB_Schema_Table $table
     * @return string the generated DDL
     */
    public function ddl_create_table(Table $table, $forced = FALSE);

    /**
     * Generates the database-specific DDL command for the table column.
     *
     * @param DB_Schema_Table $table
     * @return string the generated DDL
     */
    public function ddl_create_column(Column $column);

}