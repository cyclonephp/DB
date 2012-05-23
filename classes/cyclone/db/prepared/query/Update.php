<?php

namespace cyclone\db\prepared\query;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Update extends AbstractQuery {

    /**
     * @var \cyclone\db\query\Update
     */
    private $_query;

    public function  __construct($sql, $database, db\query\Update $query) {
        parent::__construct($sql, $database);
        $this->_query = $query;
    }
    
    public function exec() {
        return $this->_executor->exec_update($this->_prepared_stmt
            , $this->_params
            , $this->_query);
    }

}
