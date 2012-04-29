<?php

namespace cyclone\db\prepared\query;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Delete extends AbstractQuery {

    public function exec() {
        return $this->_executor->exec_delete($this->_prepared_stmt, $this->_params);
    }
    
}
