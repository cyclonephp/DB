<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class Exception extends \Exception {
    
    public $sql;

    public function __toString() {
        $rval = $this->getMessage();
        if ($this->sql) {
            $rval .= ' (query: ' . $this->sql . ')';
        }
        return $rval;
    }
}
