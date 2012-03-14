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
     * Creates a prepared query from the already built query.
     *
     * @return \cyclone\db\prepared\Query
     */
    public function prepare($database = 'default');

}
