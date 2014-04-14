<?php

use cyclone\db;
use cyclone as cy;

require __DIR__ . DIRECTORY_SEPARATOR . 'User.php';

class Record_Test extends PHPUnit_Framework_TestCase {

    private $names = array(1 => 'user1', 2 => 'user2');

    public function setUp() {
        $this->markTestSkipped('skipping db tests');
        try {
            cy\DB::connector('cytst-mysqli')->db_conn->query('truncate cy_user');
            $names = array('user1', 'user2');
            $insert = DB::insert('user');
            foreach ($names as $name) {
                $insert->values(array('name' => $name));
            }
            $insert->exec('cytst-mysqli');
        } catch (db\Exception $ex) {
            $this->markTestSkipped('skipping db tests');
        }
    }

    public function tearDown() {
        cy\DB::clear_connections();
    }


    public function testGet() {
        $user = Record_User::get(1);
        $this->assertTrue($user instanceof Record_User);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('user1', $user->name);
        $user = Record_User::get(20);
        $this->assertNull($user);
    }

    public function testSave() {
        $user = new Record_User;
        $user->name = 'user3';
        $user->save();
        $this->assertEquals(3, $user->id);

        $row = cy\DB::select()->from('user')->where('id', '=', cy\DB::esc(3))
                ->exec('cytst-mysqli')->as_array();
        $this->assertEquals($row[0], array('id' => 3, 'name' => 'user3', 'email' => null));

        $user2 = Record_User::get(2);
        $user2->name = 'user2_';
        $user2->save();
    }

    /**
     * @expectedException \cyclone\Exception
     */
    public function testGetOne() {
        $user = Record_User::get_one(array('name', '=', cy\DB::esc('user1')));
        $this->assertTrue($user instanceof Record_User);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('user1', $user->name);
        Record_User::get_one(array('id', 'in', cy\DB::expr(array(1, 2))));
    }

    public function testGetList() {
        $users = Record_User::get_list(
            array('id', 'in', cy\DB::expr(array(1, 2))),
            array('name', 'desc')
        );
        $this->assertEquals(2, count($users));
        $users = $users->as_array();
        $this->assertEquals($users[0]->id, 2);
        $this->assertEquals($users[0]->name, 'user2');
    }

    public function testGetAll() {
        $users = Record_User::get_all();
        $this->assertInstanceOf('cyclone\db\query\result\AbstractResult', $users);
        $this->assertEquals(2, count($users));
        $idx = 0;
        foreach ($users as $user) {
            $this->assertEquals($user->name, $this->names[$user->id]);
        }
    }

    public function testGetPage() {
        $users = Record_User::get_page(2, 1, array('id', 'in'
            , DB::expr(array(1, 2))))->as_array();
        $this->assertEquals(count($users), 1);
        $user = $users[0];
        $this->assertEquals('user2', $user->name);
    }

    /**
     * @expectedException cyclone\Exception
     */
    public function testDelete() {
        $this->markTestSkipped();
        $user = Record_User::get(1);
        $user->delete();
        Record_User::delete(2);
        $remaining = cy\DB::select()->from('user')->exec();
        $this->assertEquals(0, count($remaining));
        Record_User::delete();
    }

    public function testCount() {
        $count = Record_User::count(array('id', '=', cy\DB::esc(1)));
        $this->assertEquals(1, $count);
    }
}