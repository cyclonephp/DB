<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Insert extends AbstractQuery {

    /**
     * @var \cyclone\db\query\Insert
     */
    private $_query;

    public function  __construct($sql, $database, db\query\Insert $query) {
        parent::__construct($sql, $database);
        $this->_query = $query;
    }

    public function exec() {
        return $this->_executor->exec_insert($this->_prepared_stmt
            , $this->_params
            , $this->_query);
    }
}
