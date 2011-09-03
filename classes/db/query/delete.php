<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class DB_Query_Delete implements DB_Query {

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
        $this->conditions []= DB::create_expr(func_get_args());
        return $this;
    }

    public function limit($limit) {
        $this->limit = (int) $limit;
        return $this;
    }

    public function compile($database = 'default') {
        return DB::compiler($database)->compile_delete($this);
    }

    public function exec($database = 'default') {
        $sql = DB::compiler($database)->compile_delete($this);
        return DB::executor($database)->exec_delete($sql);
    }

    public function  prepare($database = 'default') {
        $sql = DB::compiler($database)->compile_delete($this);
        return new DB_Query_Prepared_Delete($sql, $database);
    }
}
