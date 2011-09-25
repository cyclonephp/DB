<?php

namespace cyclone\db\query;
use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Custom implements db\Query {

    /**
     * A raw SQL query.
     *
     * @var string
     */
    protected $sql;

    public function  __construct($sql) {
        $this->sql = $sql;
    }

    public function compile($database = 'default') {
        return $this->sql;
    }

    public function exec($database = 'default') {
        return \cyclone\DB::executor($database)->exec_custom($this->sql);
    }

    public function  prepare($database = 'default') {
        return new db\prepared\query\Custom($this->sql, $database);
    }
}
