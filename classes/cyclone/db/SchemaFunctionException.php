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
class SchemaFunctionException extends SchemaException {

    protected $_function;

    public function __construct($sql, $code, $function_name) {
        parent::__construct($sql, $code);
        $this->_function = $function_name;
    }


    public function __get($name) {
        if ($name == 'function')
            return $this->_function;

        throw new cy\Exception("property '$name' of class " . __CLASS__ . " doesn't exist or is not readable");
    }

}
