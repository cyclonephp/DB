<?php
namespace cyclone\db\prepared\executor;

use cyclone\db;
use cyclone as cy;
/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package DB
 */
class Postgres extends AbstractPreparedExecutor {

    public function prepare($sql) {
        $rval = @pg_prepare($this->_db_conn, $sql, $sql);
        if (FALSE === $rval) {
            throw new db\Exception('failed to prepare statement: ' . $sql
                 . ' (' . pg_last_error($this->_db_conn) . ')');
        }
        return $rval;
    }

    public function exec_select($prepared_stmt, array $params
            , db\query\Select $orig_query) {
        $sql = cy\DB::compiler($this->_config['config_name'])->compile_select($orig_query);
        $result = pg_execute($this->_db_conn, $sql, $params);

        return new db\query\result\Postgres($result);
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
