<?php

namespace cyclone\db\schema;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
class ForeignKey {

    /**
     * @var Table
     */
    public $local_table;

    /**
     * @var array<Column>
     */
    public $local_columns = array();

    /**
     * @var Table
     */
    public $foreign_table;

    /**
     * @var array<Column>
     */
    public $foreign_columns = array();

}