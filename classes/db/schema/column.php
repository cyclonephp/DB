<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
class DB_Schema_Column {

    /**
     * The column name
     *
     * @var string
     */
    public $name;

    /**
     * The SQL data type of the column
     * 
     * @var string
     */
    public $type;

   /**
    * Column length constraint (if any)
    *     *
    * @var int
    */
    public $length;

    /**
     * Flag marking if the column is a primary key column or not
     *
     * @var boolean
     */
    public $is_primary;

    /**
     * Raw DDL string for the column
     *
     * @var string
     */
    public $ddl;

}
