<?php

namespace cyclone\db\query;
use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Update implements db\Query {

    /**
     * The table name of the <code>UPDATE</code> query.
     *
     * @var string
     */
    public $table;

    /**
     * The values to be updated (column name => column value pairs).
     *
     * @var array
     */
    public $values;

    /**
     * The <code>WHERE</code> clause of the query. All items should be @c \cylone\db\Expression
     * instances and they will be concatenated with a top-level <code>AND</code>
     * operator during compilation.
     *
     * @var array
     */
    public $conditions;

    /**
     * The <code>LIMIT</code> clause of the query.
     *
     * @var int
     */
    public $limit;

    public $returning = array();

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function values($values) {
        $this->values = $values;
        return $this;
    }

    public function where() {
        $this->conditions []= cy\DB::create_expr(func_get_args());
        return $this;
    }

    public function limit($limit) {
        $this->limit = (int) $limit;
        return $this;
    }

    public function returning() {
        foreach (func_get_args() as $returning) {
            $this->returning []= (string) $returning;
        }
        return $this;
    }

    public function compile($database = 'default') {
        return cy\DB::compiler($database)->compile_update($this);
    }

    public function exec($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_update($this);
        return cy\DB::executor($database)->exec_update($sql, $this);
    }

    public function  prepare($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_update($this);
        return new db\prepared\query\Update($sql, $database);
    }

}
