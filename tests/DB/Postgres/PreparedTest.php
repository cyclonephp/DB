<?php
use cyclone as cy;
use cyclone\db;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DB_Postgres_PreparedTest extends Kohana_Unittest_TestCase {

    public function test_prepare() {
        $this->assertTrue(is_resource(cy\DB::executor_prepared('cytst-postgres')
            ->prepare("select * from users")), "\\cyclone\\db\\prepared\\executor\\Postgres::prepare() returns a resource");
    }

}
