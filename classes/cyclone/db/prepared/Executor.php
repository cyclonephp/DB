<?php

namespace cyclone\db\prepared;

use cyclone\db;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package DB
 */
interface Executor {

    public function prepare($sql);

    public function exec_select($prepared_stmt, array $params
            , db\query\Select $orig_query);

    public function exec_insert($prepared_stmt, array $params);

    public function exec_update($prepared_stmt, array $params);

    public function exec_delete($prepared_stmt, array $params);

}
