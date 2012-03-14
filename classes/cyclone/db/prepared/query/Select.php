<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Select extends AbstractQuery {

    /**
     * @var \cyclone\db\query\Select
     */
    private $_query;

    public function  __construct($sql, $database, db\query\Select $query) {
        parent::__construct($sql, $database);
        $this->_query = $query;
    }

    public function exec() {
        return $this->_executor->exec_select($this->_prepared_stmt
                , $this->_params, $this->_query);
    }
    
}
