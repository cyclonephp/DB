<?php

namespace cyclone\db\prepared\query;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Insert extends AbstractQuery {

    public function exec() {
        return $this->_executor->exec_insert($this->_prepared_stmt, $this->_params);
    }
}
