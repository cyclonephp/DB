<?php

namespace cyclone\db;
use cyclone as cy;

/**
 * @property-read $constraint_type string
 * @property-read $constraint_name string
 * @property-read $errcode string
 * @property-read $column string
 * @property-read $detail string
 * @property-read $hint string
 * @package db
 * @author Bence Eros<crystal@cyclonephp.org>
 */
class ConstraintException extends Exception {

    const UNIQUE_CONSTRAINT = 'unique';

    const NOTNULL_CONSTRAINT = 'notnull';

    const FOREIGNKEY_CONSTRAINT = 'foreignkey';

    const APP_CONSTRAINT = 'app';

    public function __construct($message = "", $errcode = 0
            , $constraint_type
            , $constraint_name
            , $column
            , $detail
            , $hint) {
        parent::__construct($message, $errcode);
        $this->_constraint_type = $constraint_type;
        $this->_constraint_name = $constraint_name;
        $this->_errcode = $errcode;
        $this->_column = $column;
        $this->_detail = $detail;
        $this->_hint = $hint;
    }


    protected $_constraint_type;

    protected $_constraint_name;

    protected $_errcode;

    protected $_column;

    protected $_detail;

    protected $_hint;

    public function __get($name) {
        static $enabled_attributes = array(
            'constraint_type',
            'constraint_name',
            'errcode',
            'column',
            'detail',
            'hint'
        );
        if (in_array($name, $enabled_attributes))
            return $this->{'_' . $name};

        throw new cy\Exception("property '$name' of class " . __CLASS__ . " doesn't exist or is not readable");
    }

}