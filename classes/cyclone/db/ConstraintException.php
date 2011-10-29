<?php

namespace cyclone\db;

class ConstraintException extends Exception {

    const UNIQUE_CONSTRAINT = 'unique';

    const NOTNULL_CONSTRAINT = 'notnull';

    const FOREIGNKEY_CONSTRAINT = 'foreignkey';

    const APP_CONSTRAINT = 'app';

    public $constraint_type;

    public $constraint_name;

    public $errcode;

    public $column;

    public $detail;

    public $hint;

}