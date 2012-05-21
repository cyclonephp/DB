<?php
namespace cyclone\db\prepared\executor;

use cyclone\db;
/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class Postgres extends AbstractPreparedExecutor {

    public function prepare($sql) {
        return pg_prepare($this->_db_conn, $sql, $sql);
    }

    public function exec_select($prepared_stmt, array $params
        , db\query\Select $orig_query) {
        throw new \Exception('not implemented');
    }

    public function exec_delete($prepared_stmt, array $params) {
        throw new \Exception('not implemented');
    }

    public function exec_insert($prepared_stmt, array $params) {
        throw new \Exception('not implemented');
    }

    public function exec_update($prepared_stmt, array $params) {
        throw new \Exception('not implemented');
    }

}
