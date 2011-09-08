<?php

namespace cyclone\db\query;
use cyclone\db;
use cyclone as cy;


/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Delete implements db\Query {

    /**
     * The table name of the <code>DELETE</code> query.
     *
     * @var string
     */
    public $table;

    /**
     * The WHERE conditions of the query. All items of the array should be
     * \c DB_Expression instances, and they will be concatenated by top-level
     * <code>AND</code> operators during the compilation.
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

    public function table($table) {
        $this->table = $table;
    }

    public function where() {
        $this->conditions []= cy\DB::create_expr(func_get_args());
        return $this;
    }

    public function limit($limit) {
        $this->limit = (int) $limit;
        return $this;
    }

    public function compile($database = 'default') {
        return cy\DB::compiler($database)->compile_delete($this);
    }

    public function exec($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_delete($this);
        return cy\DB::executor($database)->exec_delete($sql);
    }

    public function  prepare($database = 'default') {
        $sql = cy\DB::compiler($database)->compile_delete($this);
        return new db\prepared\query\Delete($sql, $database);
    }
}
