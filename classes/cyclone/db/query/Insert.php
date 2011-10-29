<?php

namespace cyclone\db\query;
use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
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

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function values($values) {
        $this->values []= $values;
        return $this;
    }

    public function compile($database = 'default') {
        return cy\DB::compiler($database)->compile_insert($this);
    }

    public function exec($database = 'default', $return_insert_id = TRUE) {
        $sql = cy\DB::compiler($database)->compile_insert($this);
        return cy\DB::executor($database)->exec_insert($sql, $return_insert_id, $this->table);
    }

    public function  prepare($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_insert($this);
        return new db\prepared\query\Insert($sql, $database);
    }
}
