<?php
namespace cyclone\db;

/**
 * Helper class for making the creation of @c ConstraintException instances easier.
 * All of the properties of this class are read- and writable, and the  @c build_exception()
 * method can be used to create the read-only exception instance from the already populated
 * builder properties.
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class ConstraintExceptionBuilder {

    public function __construct($message) {
        $this->message = $message;
    }

    public $message;

    public $constraint_type;

    public $constraint_name;

    public $errcode;

    public $column;

    public $detail;

    public $hint;

    /**
     * @return ConstraintException the exception instance built from the properties.
     */
    public function build_exception() {
        return new ConstraintException($this->message
            , $this->errcode
            , $this->constraint_type
            , $this->constraint_name
            , $this->column
            , $this->detail
            , $this->hint);
    }

}
