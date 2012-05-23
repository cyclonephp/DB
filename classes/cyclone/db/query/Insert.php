<?php

namespace cyclone\db\query;
use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Insert implements db\Query {

    /**
     * The table name of the <code>INSERT</code> query.
     *
     * @var string
     */
    public $table;

    /**
     * The values to be inserted.
     *
     * @var array
     */
    public $values = array();

    public $returning = array();

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function values($values) {
        $this->values []= $values;
        return $this;
    }

    /**
     * @param $returning string a column name in the database relation @c $table .
     */
    public function returning($returning) {
        foreach (func_get_args() as $arg) {
            $this->returning []= (string) $arg;
        }
        return $this;
    }

    public function compile($database = 'default') {
        return cy\DB::compiler($database)->compile_insert($this);
    }

    public function exec($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_insert($this);
        return cy\DB::executor($database)->exec_insert($sql, $this);
    }

    public function  prepare($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_insert($this);
        return new db\prepared\query\Insert($sql, $database, $this);
    }
}
