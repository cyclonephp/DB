<?php

namespace cyclone\db;
use cyclone as cy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class StmtResult extends \ArrayObject {

    protected $_affected_row_count;

    protected $_rows;

    public function __construct($rows, $affected_row_count) {
        $this->_rows = $rows;
        $this->_affected_row_count = $affected_row_count;
    }

    public function __get($name) {
        static $enabled_attributes = array('rows', 'affected_row_count');
        if (in_array($name, $enabled_attributes))
            return $this->{'_' . $name};
        throw new cy\Exception('property "' . $name . '" of class "' . __CLASS__ . '" does not exist');
    }


}
