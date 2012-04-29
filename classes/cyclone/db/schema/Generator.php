<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * An implementing class should exist for each DBMS supported by CyclonePHP.
 *
 * Implementations are able to build proper DDL strings from the PHP objects that
 * represent database objects. These PHP objects are @c Table
 * and @c Column .
 * 
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Generator {

    /**
     * Generates the database-specific DDL command for the table.
     *
     * @param Table $table
     * @param boolean $forced
     * @return string the generated DDL
     */
    public function ddl_create_table(Table $table, $forced = FALSE);

    /**
     * Generates the database-specific DDL command for the table column.
     *
     * @param Column $column
     * @return string the generated DDL
     */
    public function ddl_create_column(Column $column);

}
