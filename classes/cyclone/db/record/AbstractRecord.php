<?php

namespace cyclone\db\record;
use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
abstract class AbstractRecord {

    /**
     * classname =&gt; @c Schema instance pairs
     *
     * @var array
     */
    private static $_schemas = array();

    /**
     * Holds the data of the database row represented by the object.
     *
     * @var array
     */
    protected $_row = array();

    /**
     * Holds the transient properties of the object that hasn't got
     * suitable database fields
     *
     * @var array
     */
    protected $_transient_data = array();

    /**
     * Subclasses should implement this method and should return the mapping schema
     * for the given record class.
     *
     * @return Schema
     */
    protected static function setup() {

    }

    /**
     * Gets the mapping schema for the class this method has been called on.
     * If the schema already exists in the internal cache then it is returned
     * immediately, otherwise the @c setup() method of the actual subclass will
     * be called, its return value (which should be the appropriate @c Schema instance)
     * will be cached and returned.
     *
     * @return Schema
     */
    protected static function schema() {
        $classname = get_called_class();
        if ( ! isset(self::$_schemas[$classname])) {
            $schema = call_user_func(array(get_called_class(), 'setup'));
            $schema->class = $classname;
            self::$_schemas[$classname] = $schema;
        }
        return self::$_schemas[$classname];
    }

    /**
     * Returns a record object that represents the database row
     * that owns the primary key specified by the <code>$id</code> parameter
     *
     * Example: @code //getting the user with id = 1
     * $user = UserRecord::get(1); @endcode
     *
     * @param int/mixed $id
     * @return AbstractRecord
     */
    public static function get($id) {
        $schema = static::schema();
        $query = cy\DB::select()
                ->from($schema->table_name)
                ->where($schema->primary_key, '=', DB::esc($id))
                ->exec($schema->database)
                        ->rows($schema->class)->as_array();
        if (empty($query))
            return null;
        return $query[0];

    }

    /**
     * Returns one entity that matches the conditions given by the arguments.
     *
     * If 0 row is found the <code>NULL</code> will be returned, if 1 row is found
     * then a @c AbstractRecord subclass instance will be returned representing the
     * found row, if more than one row is found then an @c cyclone\db\Exception
     * will be thrown.
     *
     * The method accepts any number of array arguments, every argument should be
     * a 3-element numeric array where
     * <ol>
     *  <li>the first element must be a database column or @c cyclone\db\Expression instance</li>
     *  <li>the second element must be a database operator (string)</li>
     *  <li>the third element must be a database column or @c cyclone\db\Expression instance</li>
     * </ol>
     *
     * @code $user = UserRecord::get_one(
     *   array('email', '=', cy\DB::esc('helloworld@example.org'))); @endcode
     *
     *
     * @return AbstractRecord
     * @throws cyclone\db\Exception
     */
    public static function get_one() {
        $schema = static::schema();
        $query = cy\DB::select()->from($schema->table_name);
        $args = func_get_args();
        static::build_sfw($query, $args);
        $result = $query->exec($schema->database)->rows($schema->class)->as_array();
        switch(count($result)) {
            case 1: return $result[0];
            case 0: return null;
            default: throw new db\Exception('more than one results: ' . $query->compile($schema->database));
        }
    }

    /**
     * Runs a SELECT * FROM &lt;table&gt; WHERE .. ORDER BY ... query and returns
     * the result as an array of active records. The &lt;table&gt; is always the
     * table name of the current schema.
     *
     * The method accepts a variable length argument list, every argument must be
     * a numeric array.
     *
     * The <code>WHERE</code> clause can be defined by the 3-element arrays in the
     * argument list, where
     * <ol>
     *  <li>the first element must be a database column or @c cyclone\db\Expression instance</li>
     *  <li>the second element must be a database operator (string)</li>
     *  <li>the third element must be a database column or @c cyclone\db\Expression instance</li>
     * </ol>
     *
     * The <code>ORDER BY</code> clause of the query can be defined using 2-elements
     * arrays of the argument list. Every array shouls contain
     * <ol>
     *      <li>the order column (string)</li>
     *      <li>the order direction (string, <code>'ASC'</code> or <code>'DESC'</code>)</li>
     * </ol>
     *
     * Example:
     * @code $example_users = UserRecord::get_list(array('email', 'LIKE', cy\DB::esc('%example.org'))
     *      , array('name', 'desc')); @endcode
     *
     * @return array<AbstractRecord>
     */
    public static function get_list() {
        $schema = static::schema();
        $query = cy\DB::select()->from($schema->table_name);
        $args = func_get_args();
        static::build_sfw($query, $args);
        return $query->exec($schema->database)->rows($schema->class);
    }

    /**
     * Select all rows from the table of the current schema and returns them
     * as an array of active records representing the rows. The same as
     * calling @c get_list() without arguments.
     *
     * @return array<AbstractRecord>
     */
    public static function get_all() {
        $schema = static::schema();
        return cy\DB::select()->from($schema->table_name)
                ->exec($schema->database)->rows($schema->class);
    }

    /**
     * Returns the given page from the table. The first <code>$page</code>
     * and <code>$page_size</code> parameters are used to create the OFFSET - LIMIT
     * clauses of the query, the optional following arguments can be used as
     * WHERE and ORDER BY clause definitions as at @c get_list() .
     * Example:
     *
     * @code   // returns the 31. - 60. rows from the table
     * $users = UserRecord::inst()->get_page(2, 30); @endcode
     *
     * @param int $page
     * @param int $page_size
     * @return array<AbstractRecord>
     */
    public static function get_page($page, $page_size) {
        $schema = static::schema();
        $query = cy\DB::select()->from($schema->table_name);
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        static::build_sfw($query, $args);
        static::paginate($page, $page_size, $query);
        return $query->exec($schema->database)->rows($schema->class);
    }

    protected static function paginate($page, $page_size, db\query\Select $query) {
        $query->offset(($page - 1) * $page_size)->limit($page_size);
    }

    protected static function build_sfw(db\query\Select $query, $args) {
        foreach ($args as $arg) {
            if ( ! is_array($arg))
                throw new Exception("$arg is not an array");
            switch (count($arg)) {
                case 2: $query->order_by($arg[0], $arg[1]); break;
                case 3: $query->where($arg[0], $arg[1], $arg[2]); break;
                default: throw new db\Exception('arguments must be 2 or 3 length arrays and not '.  count($arg));
            }
        }
    }

    /**
     * This method can be called both on the singleton instance of the entity class
     * and any other instances too. In the first case it accepts a mandatory primary
     * key parameter which is the primary key value of the row to be deleted from
     * the table of the actual schema, in the second case it deletes the row of the current
     * instance from the database. Examples:
     *
     * @code
     * UserRecord::delete(3);
     *
     * $user->id = 3;
     * $user->delete(); @endcode
     *
     * If we have a an <code>UserRecord</code> active record class mapped to a
     * <code>users</code> table with <code>id</code> as primary key column then
     * both calls will result in executing the following SQL: <code>DELETE
     * FROM `users` WHERE id = 3</code>.
     *
     * @return int
     */
    public function delete() {
        $schema = static::schema();
        switch (func_num_args()) {
            case 0:
                if (is_null($this->_row))
                    throw new db\Exception('static singleton instances can not be deleted');
                return cy\DB::delete($schema->table_name)->where($schema->primary_key, '=', DB::esc($this->_row[$schema->primary_key]))
                        ->exec($schema->database);
                break;
            case 1:
                $id = func_get_arg(0);
                return cy\DB::delete($schema->table_name)->where($schema->primary_key, '=', DB::esc($id))
                    ->exec($schema->database);
                break;
            default:
                throw new db\Exception('delete() method can be called at most with 1 parameter');
        }
    }

    public static function count() {
        $schema = static::schema();
        $query = cy\DB::select(array(cy\DB::expr('count(1)'), 'count'))->from($schema->table_name);
        $args = func_get_args();
        static::build_sfw($query, $args);
        $result = $query->exec($schema->database)->as_array();
        return $result[0]['count'];
    }

    /**
     * If the primary key value exists in the row data then will update the
     * database row, otherwise insert it and assign the generated primary key
     * to the primary key value in the row data.
     *
     * @uses insert()
     * @uses update()
     */
    public function save() {
        $schema = $this->schema();
        if (array_key_exists($schema->primary_key, $this->_row)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * Runs an SQL <code>INSERT</code> statement insertint the actual row data.
     */
    public function insert() {
        $schema = $this->schema();
        $this->{$schema->primary_key} = cy\DB::insert($schema->table_name)
                    ->values($this->_row)
                    ->returning($schema->primary_key)
                    ->exec($schema->database)->rows[0][$schema->primary_key];
    }

    /**
     * Runs and SQL <code>UPDATE</code> statement updating the current row data.
     * If the primary key value doesn't exist in the actual row data then it
     * doesn't have any effect.
     */
    public function update() {
        $schema = $this->schema();
        DB::update($schema->table_name)->values($this->_row)
                ->where($schema->primary_key, '=', DB::esc($this->_row[$schema->primary_key]))
                ->exec($schema->database);
    }

    public function  __get($name) {
        if (array_key_exists($name, $this->schema()->columns)) {
            return cy\Arr::get($this->_row, $name);
        } elseif (array_key_exists($name, $this->_transient_data)) {
            return $this->_transient_data[$name];
        }
        throw new db\Exception('trying to read non-existent property: '.$name);
    }

    public function  __set($name, $value) {
        if (array_key_exists($name, $this->schema()->columns)) {
            $this->_row[$name] = $value;
        } else {
            $this->_transient_data[$name] = $value;
        }
    }

    public function  __unset($name) {
        if (array_key_exists($name, $this->_row)) {
            unset($this->_row[$name]);
        } elseif (array_key_exists($name, $this->_transient_data)) {
            unset($this->_transient_data[$name]);
        }
    }

    public function  __isset($name) {
        return array_key_exists($name, $this->_row)
                || array_key_exists($name, $this->_transient_data);
    }

    /**
     * Returns the entity properties as an array (the internal row data).
     *
     * @return array
     */
    public function as_array() {
        return $this->_row;
    }
    
}
