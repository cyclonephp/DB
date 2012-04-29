<?php

namespace cyclone;

/**
 * Wrapper class of static factory methods for creating database query objects.
 * Read the library manual for examples of using it.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class DB {

    /**
     * Object pool for the compiler instances.
     *
     * @var array<\cyclone\dbCompiler>
     */
    private static $_compilers = array();

    /**
     * Object pool for the executor instances.
     *
     * @var array<\cyclone\db\Executor>
     */
    private static $_executors = array();

    /**
     * Object pool for the prepared statement executor instances.
     *
     * @var array<\cyclone\db\prepared\Executor>
     */
    private static $_executor_prepareds = array();

    /**
     * Object pool for the connector instances.
     *
     * @var array<\cyclone\db\ConnectorConnector>
     */
    private static $_connectors = array();

    /**
     * Object pool for the schema generator instances.
     *
     * @var array
     */
    private static $_schema_generators = array();

    /**
     * Returns the SQL compiler of the database adapter of the <code>$config</code>
     * connection.
     *
     * The compiler instances are re-used (pooled) on subsequent calls, therefore
     * only one compiler instance is created for a given DBMS.
     *
     * @param string $config config file name
     * @return \cyclone\db\Compiler
     */
    public static function compiler($config = 'default') {
        if ( ! isset(self::$_compilers[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $cfg['config_name'] = $config;
            $class = '\\cyclone\\db\\compiler\\'.ucfirst($cfg['adapter']);
            self::$_compilers[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_compilers[$config];
    }

    /**
     * Returns an @c Executor instance which is able to run an SQL query
     * on the <code>$config</code> database connection.
     *
     * The executor instances are re-used (pooled) on subsequent calls, therefore
     * only one executor is created for a given DBMS.
     *
     * @param string $config config file name
     * @return \cyclone\db\Executor
     */
    public static function executor($config = 'default') {
        if ( ! isset(self::$_executors[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $cfg['config_name'] = $config;
            $class = '\\cyclone\\db\\executor\\'.ucfirst($cfg['adapter']);
            self::$_executors[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_executors[$config];
    }

    /**
     * Static factory method for @c \cyclone\db\prepared\Executor instances.
     * The prepared statement executor of the adapter of the <code>$config</code>
     * connection will be returned.
     *
     * The instances are re-used (pooled) on subsequent calls, therefore only
     * one executor is created for a given DBMS.
     *
     * @param string $config config file name
     * @return \cyclone\db\prepared\Executor
     */
    public static function executor_prepared($config = 'default') {
        if ( ! isset(self::$_executor_prepareds[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $cfg['config_name'] = $config;
            $class = '\\cyclone\\db\\prepared\\executor\\'.ucfirst($cfg['adapter']);
            self::$_executor_prepareds[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_executor_prepareds[$config];
    }

    /**
     * Static factory method for @c \cyclone\db\Connector instances.
     * Returns the connector instance which is able to connect to the DBMS
     * handled by the adapter of the <code>$config</code> connection.
     *
     * @param string $config config file name
     * @return \cyclone\db\Connector
     */
    public static function connector($config = 'default') {
        if ( ! isset(self::$_connectors[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $cfg['config_name'] = $config;
            $class = '\\cyclone\\db\\connector\\'.ucfirst($cfg['adapter']);
            self::$_connectors[$config] = new $class($cfg);
        }
        return self::$_connectors[$config];
    }

    /**
     * Returns the database schema generator instance of the adapter of the
     * given connection.
     *
     * @param string $config config file name
     * @return \cyclone\db\schema\Generator
     */
    public static function schema_generator($config = 'default') {
        if ( ! isset(self::$_schema_generators[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $cfg['config_name'] = $config;
            $class = '\\cyclone\\db\\schema\\generator\\'.ucfirst($cfg['adapter']);
            self::$_schema_generators[$config] = new $class($cfg);
        }
        return self::$_schema_generators[$config];
    }

    /**
     * Helper factory method for custom SQL queries.
     *
     * @param string $sql
     * @return \cyclone\db\query\Custom
     */
    public static function query($sql) {
        return new db\query\Custom($sql);
    }

    /**
     * Helper factory method for SQL SELECT queries.
     *
     * @return \cyclone\db\query\Select
     */
    public static function select() {
        $query = new db\query\Select;
        $args = func_get_args();
        $query->columns_arr($args);
        return $query;
    }

    /**
     * Helper factory method for SQL SELECT DISTINCT queries.
     *
     * @return db\query\Select
     */
    public static function select_distinct() {
        $query = new db\query\Select;
        $query->distinct = TRUE;
        $args = func_get_args();
        $query->columns_arr($args);
        return $query;
    }

    /**
     * Helper factory method for creating objects representing SQL UPDATE
     * statements.
     *
     * @param string $table the table name to be updated
     * @return db\query\Update
     */
    public static function update($table = null) {
        $query = new db\query\Update;
        $query->table = $table;
        return $query;
    }

    /**
     * Helper factory method for creating objects representing SQL INSERT statements.
     *
     * @param string $table the table to insert into
     * @return \cyclone\db\query\Insert
     */
    public static function insert($table = null) {
        $query = new db\query\Insert;
        $query->table = $table;
        return $query;
    }

    /**
     * Helper factory method for creating objects representing SQL DELETE statements.
     *
     * @param string $table the table to delete from
     * @return \cyclone\db\query\Delete
     */
    public static function delete($table = null) {
        $query = new db\query\Delete;
        $query->table = $table;
        return $query;
    }

    /**
     * Helper factory method for creating objects representing database expressions.
     *
     * Examples: @code
     *
     * use cyclone as cy;
     * //...
     *
     * // binary operator expression
     * $expr = cy\DB::expr('a', '=', 'b');
     *
     * // binary expression with subselect
     * $expr = cy\DB::expr('id', 'IN', cy\DB::select('id')->from('users'));
     *
     * // binary expression with set expression as right operand
     * $expr = cy\DB::expr('id', 'NOT IN', cy\DB::expr(array(1, 2, 3)));
     *
     * // unary expression with subselect
     * $expr = cy\DB::expr('EXISTS', cy\DB::select('id')->from('users')
     *      ->where('group_id', '=', NULL));
     *
     * // creating a more complex expression
     * $expr = cy\DB::expr(cy\DB::expr('name', '=', cy\DB::esc($username))
     *      , 'OR'
     *      , cy\DB::expr('email', '=', cy\DB::esc($email))); @endcode
     *
     * @return \cyclone\db\query\Expression
     */
    public static function expr() {
        return self::create_expr(func_get_args());
    }

    /**
     * Helper factory method for creating objects representing database expressions.
     * Similar behavior to @c DB::expr() but it takes an array as its argument
     * instead of using variable length argument list.
     *
     * @param array $args
     * @return \cyclone\db\query\Expression
     */
    public static function create_expr($args) {
        switch (count($args)) {
            case 1:
                if (is_array($args[0])) {
                    return new db\SetExpression($args[0]);
                }
                return new db\CustomExpression($args[0]);
            case 2:
                return new db\UnaryExpression($args[0], self::create_nullexpr($args[1]));
            case 3:
                return new db\BinaryExpression(self::create_nullexpr($args[0])
                        , $args[1]
                        , self::create_nullexpr($args[2]));
        }
    }

    protected static function create_nullexpr($arg) {
        if (null === $arg) {
            return new db\CustomExpression('NULL');
        } else {
            return $arg;
        }
    }

    public static function clear_connections() {
        foreach (self::$_connectors as $connector) {
            $connector->disconnect();
        }
        self::$_compilers = array();
        self::$_connectors = array();
        self::$_executors = array();
        self::$_executor_prepareds = array();
    }

    /**
     * @param scalar $param
     * @return db\ParamExpression
     */
    public static function esc($param) {
        return new db\ParamExpression($param);
    }

    /**
     *
     * @param string $name
     * @return db\CustomExpression
     */
    public static function param($name = '?') {
        return new db\CustomExpression($name);
    }
    
}
