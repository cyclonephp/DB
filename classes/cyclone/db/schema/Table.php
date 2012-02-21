<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Table {

    private static $_instances = array();

    /**
     *
     * @param string $name
     * @return Table
     */
    public static function get_by_name($name) {
        if (isset(self::$_instances[$name]))
            return self::$_instances[$name];
        return new Table($name);
    }

    public function __construct($name) {
        if (isset(self::$_instances[$name]))
            throw new db\Exception('Table instance for table "' . $name . '" already created');

        self::$_instances[$name] = $this;
        $this->name = $name;
    }

    /**
     *
     * @param string $name
     * @return Column
     */
    public function create_column($name) {
        return $this->columns []= new Column($this, $name);
    }


    public static function for_record_schema(db\record\Schema $schema) {
        $rval = new Table;
        $rval->database = $schema->database;
        $rval->table_name = $schema->table_name;
        foreach ($schema->columns as $colname => $ddl) {
            $col = new Column($rval, $colname);
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
     * Column name > Column pairs
     *
     * @var array
     */
    public $columns = array();

    public $foreign_keys = array();
}
