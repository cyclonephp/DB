<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * Value object representing a database column. It is used by database schema
 * generators to store internally the database schema to be created
 * and @c cyclone\db\schema\generator\AbstractGenerator subclass instances can
 * generate DBMS-specific DDL strings from it.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Column {

    /**
     * Assoc. array with table-name => array-of-columns pairs. Every array of
     * columns is a pair of column-name => @c Column instance pairs. This
     * instance pool is maintained by @c get_for() and @c __construct()
     *
     * @var array
     */
    private static $_instances = array();

    public static function get_by_table($table_name) {
        if ( ! isset(self::$_instances[$table_name])) {
            self::$_instances[$table_name] = array();
        }
        return self::$_instances[$table_name];
    }

    /**
     * @param Table | string $table
     * @param string $col_name
     * @return Column
     */
    public static function get_for($table, $col_name) {
        if (is_object($table)) {
            if ( ! $table instanceof Table)
                throw new db\Exception('$table argument must be a string or a Table instance');

            $table_name = $table->name;
        } else {
            $table_name = $table;
            $table = Table::get_by_name($table_name);
        }
        if ( ! isset(self::$_instances[$table_name])) {
            self::$_instances[$table_name] = array();
        }
        if (isset(self::$_instances[$table_name][$col_name]))
            return self::$_instances[$table_name][$col_name];

        return new Column($table, $col_name);
    }

    public static function clear_pool() {
        self::$_instances = array();
    }

    /**
     * @param Table $table the owner table
     * @param string name the column name
     * @throws \cyclone\db\Exception if there is an existing column with the same
     *  name in the same table.
     */
    public function __construct(Table $table, $name) {
        $this->table = $table;
        $this->name = $name;
        if ( ! isset(self::$_instances[$table->name])) {
            self::$_instances[$table->name] = array();
        }
        if (isset(self::$_instances[$table->name][$name]))
            throw new db\Exception("column {$table->name}.{$name} already created");

        self::$_instances[$table->name][$name] = $this;
    }

    /**
     * The table which this column belongs to
     *
     * @var Table
     */
    public $table;
    
    /**
     * The column name
     *
     * @var string
     */
    public $name;

    /**
     * The SQL data type of the column
     * 
     * @var string
     */
    public $type;

   /**
    * Column length constraint (if any)
    *     *
    * @var int
    */
    public $length;

    /**
     * Flag marking if the column has a NOT NULL constraint.
     *
     * @var boolean
     */
    public $not_null;

    /**
     * Flag marking if the column is a primary key column or not
     *
     * @var boolean
     */
    public $is_primary;

    /**
     * Raw DDL string for the column
     *
     * @var string
     */
    public $ddl;

}
