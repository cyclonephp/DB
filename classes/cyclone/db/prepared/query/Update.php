<?php

namespace cyclone\db\prepared\query;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class Update extends AbstractQuery {
    
    public function exec() {
        return $this->_executor->exec_update($this->_prepared_stmt, $this->_params);
    }

}
