<?php

namespace cyclone\db\schema;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 * @property-read array<ForeignKey> $foreign_keys
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

    /**
     * A sequence of foreign keys associated with the table.
     *
     * @var array<ForeignKey>
     * @usedby add_foreign_key()
     */
    public $_foreign_keys = array();

    private static $_enabled_attributes = array(
        'foreign_keys'
    );

    public function __get($name) {
        if (in_array($name, self::$_enabled_attributes))
            return $this->{'_' . $name};
            
        throw new db\Exception("property $name doesn't exist or is not readable");
    }

    /**
     *
     * @param string $name
     * @return Column
     */
    public function create_column($name) {
        return $this->columns []= new Column($this, $name);
    }

    /**
     * Adds a new foreign key object to \c $foreign_keys if the new foreign key
     * is not present yet.
     *
     * If the <code>$local_table</code> of the foreign key is <code>NULL</code>
     * then it will default to <code>$this</code>. Otherwise if the local table of
     * the foreign key is not <code>$this</code> then a \c \cyclone\db\Exception
     * will be thrown.
     *
     * @param ForeignKey $new_fk
     * @uses Foreignkey::equals()
     * @see ForeignKey::$local_table
     */
    public function add_foreign_key(ForeignKey $new_fk) {
        $exists = FALSE;
        foreach ($this->foreign_keys as $old_fk) {
            if ($old_fk->equals($new_fk)) {
                $exists = TRUE;
                break;
            }
        }
        if ($exists)
            return;

        if (NULL === $new_fk->local_table) {
            $new_fk->local_table = $this;
        }
        if ($new_fk->local_table !== $this)
            throw new db\Exception("could not attach a foreign key to table {$this->name} with local table {$new_fk->local_table->name}");

        $this->foreign_keys []= $new_fk;
    }
}
