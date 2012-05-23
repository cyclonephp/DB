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

    /**
     * @expectedException cyclone\db\Exception
     */
    public function test_prepare_failure() {
        $stmt = cy\DB::executor_prepared('cytst-postgres')->prepare('select * from dummy');
    }

    public function test_exec_select() {
        $result = cy\DB::select('id', 'name')->from('users')->prepare('cytst-postgres')->exec();
        $this->assertEquals(2, count($result));
    }

}
