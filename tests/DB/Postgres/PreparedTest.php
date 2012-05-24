<?php
use cyclone as cy;
use cyclone\db;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DB_Postgres_PreparedTest extends DB_Postgres_DbTest {

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
        $result = cy\DB::select('id', 'name')->from('users')->prepare('cytst-postgres')
            ->exec()->as_array();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('user1', $result[0]['name']);
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals('user2', $result[1]['name']);
    }

    public function test_exec_insert() {
        $result = cy\DB::insert('users')->values(array('name' => 'user3'))
            ->returning('id')
            ->prepare('cytst-postgres')->exec();
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(1, $result->affected_row_count);
        $this->assertEquals(3, $result->rows[0]['id']);
    }

    public function test_exec_update() {
        $result = cy\DB::update('users')->values(array('name' => 'u'))
            ->where('id', '=', cy\DB::esc(1))
            ->returning('id', 'name')
            ->prepare('cytst-postgres')->exec();
        $this->assertInstanceOf('cyclone\\db\\StmtResult', $result);
        $this->assertEquals(1, $result->affected_row_count);
        $this->assertEquals(1, $result->rows[0]['id']);
        $this->assertEquals('u', $result->rows[0]['name']);
    }

    public function test_exec_delete() {
        $result = cy\DB::delete('users')->prepare('cytst-postgres')->exec();
        $this->assertEquals(2, $result->affected_row_count);
    }

}
