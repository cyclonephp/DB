<?php

namespace cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
interface Query {

    public function compile($database = 'default');

    public function exec($database = 'default');

    /**
     * @return DB_Query_Prepared
     */
    public function prepare($database = 'default');

}
