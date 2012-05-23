<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Delete extends AbstractQuery {

    /**
     * @var \cyclone\db\query\Delete
     */
    private $_query;

    public function  __construct($sql, $database, db\query\Delete $query) {
        parent::__construct($sql, $database);
        $this->_query = $query;
    }


    public function exec() {
        return $this->_executor->exec_delete($this->_prepared_stmt
            , $this->_params
            , $this->_query);
    }
    
}
