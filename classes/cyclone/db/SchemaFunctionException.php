<?php
namespace cyclone\db;
use cyclone as cy;

/**
 * Thrown by executors (in \cyclone\db\executor namespace) and prepared executors
 * (\cyclone\db\prepared\executor namespace) in cases when one of the database functions
 * called by the problematic query doesn't exist.
 *
 * @property-read $function_name string the name of the missing database function.
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class SchemaFunctionException {

    protected $_function_name;

    public function __get($name) {
        if ($name == 'function_name')
            return $this->_function_name;

        throw new cy\Exception("property '$name' of class " . __CLASS__ . " doesn't exist or is not readable");
    }

}
