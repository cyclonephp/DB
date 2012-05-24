<?php

namespace cyclone\db;

/**
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
        $this->constraint_type = $constraint_type;
        $this->constraint_name = $constraint_name;
        $this->errcode = $errcode;
        $this->column = $column;
        $this->detail = $detail;
        $this->hint = $hint;
    }


    public $constraint_type;

    public $constraint_name;

    public $errcode;

    public $column;

    public $detail;

    public $hint;

}