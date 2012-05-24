<?php
namespace cyclone\db;
use cyclone as cy;

/**
 * Thrown by executors (in \cyclone\db\executor namespace) and prepared executors
 * (\cyclone\db\prepared\executor namespace) in cases when one of the relations
 * referenced in the problematic query doesn't exist.
 *
 * @property-read $relation string the name of the missing relation.
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class SchemaRelationException {

    protected $_relation;

    public function __get($name) {
        if ($name == 'relation')
            return $this->_relation;

        throw new cy\Exception("property '$name' of class " . __CLASS__ . " doesn't exist or is not readable");
    }

}
