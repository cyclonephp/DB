<?php

namespace cyclone;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
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
     * @var array<DB_Executor_Prepared>
     */
    private static $_executor_prepareds = array();

    /**
     * Object pool for the connector instances.
     *
     * @var array<DB_Connector>
     */
    private static $_connectors = array();

    /**
     * Object pool for the schema generator instances.
     *
     * @var array
     */
    private static $_schema_generators = array();

    /**
     * @param string $config config file name
     * @return DB_Compiler
     */
    public static function compiler($config = 'default') {
        if ( ! isset(self::$_compilers[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $class = '\\cyclone\\db\\compiler\\'.ucfirst($cfg['adapter']);
            self::$_compilers[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_compilers[$config];
    }

    /**
     * @param string $config config file name
     * @return DB_Executor
     */
    public static function executor($config = 'default') {
        if ( ! isset(self::$_executors[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $class = '\\cyclone\\db\\executor\\'.ucfirst($cfg['adapter']);
            self::$_executors[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_executors[$config];
    }

    /**
     * @param string $config config file name
     * @return DB_Executor_Prepared
     */
    public static function executor_prepared($config = 'default') {
        if ( ! isset(self::$_executor_prepareds[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $class = '\\cyclone\\db\\prepared\\executor\\'.ucfirst($cfg['adapter']);
            self::$_executor_prepareds[$config] = new $class($cfg, DB::connector($config)->db_conn);
        }
        return self::$_executor_prepareds[$config];
    }

    /**
     * @param string $config config file name
     * @return DB_Connector
     */
    public static function connector($config = 'default') {
        if ( ! isset(self::$_connectors[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $class = '\\cyclone\\db\\connector\\'.ucfirst($cfg['adapter']);
            self::$_connectors[$config] = new $class($cfg);
        }
        return self::$_connectors[$config];
    }

    /**
     * @param string $config config file name
     * @return DB_Schema_Generator
     */
    public static function schema_generator($config = 'default') {
        if ( ! isset(self::$_schema_generators[$config])) {
            $cfg = Config::inst()->get('db/'.$config);
            $class = '\\cyclone\\db\\schema\\generator\\'.ucfirst($cfg['adapter']);
            self::$_schema_generators[$config] = new $class($cfg);
        }
        return self::$_schema_generators[$config];
    }

    /**
     * Helper factory method for custom SQL queries.
     *
     * @param string $sql
     * @return DB_Query_Custom
     */
    public static function query($sql) {
        return new db\query\Custom($sql);
    }

    /**
     * Helper factory method for SQL SELECT queries.
     *
     * @return DB_Query_Select
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
     * Helper factory method for SQL UPDATE statements.
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
     * Helper factory method for SQL INSERT statements.
     *
     * @param string $table the table to insert into
     * @return DB_Query_Insert
     */
    public static function insert($table = null) {
        $query = new db\query\Insert;
        $query->table = $table;
        return $query;
    }

    /**
     * Helper factory method for SQL DELETE statements.
     *
     * @param string $table the table to delete from
     * @return DB_Query_Delete
     */
    public static function delete($table = null) {
        $query = new db\query\Delete;
        $query->table = $table;
        return $query;
    }

    public static function expr() {
        return self::create_expr(func_get_args());
    }

    /**
     * @param array $args
     * @return DB_Expression
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

    public static function esc($param) {
        return new db\ParamExpression($param);
    }

    public static function param($name = '?') {
        return new db\CustomExpression($name);
    }
    
}
