<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Table {

    public static function for_record_schema(db\record\Schema $schema) {
        $rval = new Table;
        $rval->database = $schema->database;
        $rval->table_name = $schema->table_name;
        foreach ($schema->columns as $colname => $ddl) {
            $col = new Column;
            $col->name = $colname;
            $col->ddl = $ddl;
            $rval->columns[$colname] = $col;
        }
        return $rval;
    }

    /**
     * The name of the database connection configuration to be used when working
     * with this instance (eg. when creating DDL from it)
     *
     * @var string
     */
    public $database;

    /**
     * The name of the database table
     *
     * @var string
     */
    public $table_name;

    /**
     * Column name => DB_Schema_Column pairs
     *
     * @var array
     */
    public $columns = array();
}
