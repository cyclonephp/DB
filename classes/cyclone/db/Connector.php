<?php

namespace cyclone\db;
/**
 * Represents a database connection.
 *
 * Responsible for connecting/disconnecting and basic transaction handling in the
 * database connection. Every adapters should provide an implementation. Exactly
 * one <code>Connector</code> instance belongs to each database connections.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Connector {

    public function connect();

    public function disconnect();

    public function commit();

    public function rollback();

    /**
     * @param boolean $autocommit
     */
    public function autocommit($autocommit);

}
