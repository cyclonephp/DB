<?php
namespace cyclone;

use cyclone as cy;
/**
 * Thrown by executors (in \cyclone\db\executor namespace) and prepared executors
 * (\cyclone\db\prepared\executor namespace) in cases when one of the columns
 * referenced in the problematic query doesn't exist.
 *
 * @property-read $relation string the name of the relation which' column is missing
 * @property-read $column the name of the missing column
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class SchemaColumnException {

    protected $_relation;

    protected $_column;

    public function __get($name) {
        static $enabled_attributes = array('relation', 'column');
        if (in_array($name, $enabled_attributes))
            return $this->{'_' . $name};

        throw new cy\Exception("property '$name' of class " . __CLASS__ . " doesn't exist or is not readable");
    }

}
