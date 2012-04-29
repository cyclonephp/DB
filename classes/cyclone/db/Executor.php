<?php

namespace cyclone\db;

/**
 * Interface for classes that are able to execute an SQL query on a given
 * DBMS type, using the appropriate php functions and methods.
 * 
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Executor {

    public function exec_select($select_sql);

    /**
     * @param $insert_sql
     * @param query\Insert|null $orig_query
     * @return \cyclone\db\StmtResult
     */
    public function exec_insert($insert_sql, query\Insert $orig_query = NULL);

    /**
     * @param $update_sql
     * @return \cyclone\db\StmtResult
     */
    public function exec_update($update_sql, query\Update $orig_query = NULL);

    /**
     * @abstract
     * @param $delete_sql
     * @return \cyclone\db\StmtResult
     */
    public function exec_delete($delete_sql, query\Delete $orig_query = NULL);
}
