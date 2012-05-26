<?php

namespace cyclone\db;
/**
 * Represents a database connection.
 *
 * Responsible for connecting/disconnecting and basic transaction handling in the
 * database connection. Every adapters should provide an implementation. At most
 * one <code>Connector</code> instance belongs to each database connections.
 *
 * The <code>Connector</code> implementation classes must be in the
 * <code>\cyclone\db\connector</code> namespace and the class name should be
 * the name of the DBMS type handled by the connector.
 *
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Connector {

    /**
     * Created connection to the database.
     *
     * @return void
     * @throws \cyclone\db\Exception if the connection fails.
     */
    public function connect();

    /**
     * Disconnects from the database
     *
     * @return void
     * @throws \cyclone\db\Exception if the disconnection fails.
     */
    public function disconnect();

    /**
     * Commits a database transaction.
     *
     * @throws \cyclone\db\Exception if no transaction is started.
     */
    public function commit();

    /**
     * Rolls back a database transaction.
     *
     * @throws \cyclone\db\Exception if no transaction is started.
     */
    public function rollback();

    /**
     * Starts a database transaction.
     *
     * @throws \cyclone\db\Exception if fails to start the transaction or
     *   there is an already started transaction in the connection and the
     *   DBMS doesn't support nested transactions.
     */
    public function start_transaction();

}
