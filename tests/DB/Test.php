<?php

use cyclone as cy;
use cyclone\db;



//require_once __DIR__ . '/Mysqli/DBTest.php';

class DB_Test extends PHPUnit_Framework_TestCase {

    public function testPools() {
        $this->assertInstanceOf('\\cyclone\\db\\Compiler', cy\DB::compiler('cytst-mysqli'));
        $this->assertInstanceOf('\\cyclone\\db\\Executor', cy\DB::executor('cytst-mysqli'));
        $this->assertInstanceOf('\\cyclone\\db\\Connector', cy\DB::connector('cytst-mysqli'));
        cy\DB::connector('cytst-mysqli')->disconnect();
    }

    public function testQueryFactory() {
        $query = cy\DB::select();
        $this->assertEquals($query->columns, array(cy\DB::expr('*')));

        $query = cy\DB::update('user');
        $this->assertEquals($query->table, 'user');

        $query = cy\DB::insert('user');
        $this->assertEquals($query->table, 'user');

        $query = cy\DB::delete('user');
        $this->assertEquals($query->table, 'user');
    }

    public function testExpressionFactory() {
        $expr = cy\DB::expr('a', '=', 'b');
        $this->assertTrue($expr instanceof db\BinaryExpression);

        $expr = cy\DB::expr('exists', cy\DB::select());
        $this->assertTrue($expr instanceof db\UnaryExpression);
    }

    public function testQuerySelect() {
        $query = cy\DB::select()->from('user')
                ->join('group')->on('user.group_fk', '=', 'group.id')
                ->where('exists', DB::select()->from('user'))
                ->group_by('id', 'name')
                ->having('hello', '=', 'world')
                ->offset(2)
                ->limit(10);
    }

    public function testQueryDelete() {
        $query = cy\DB::delete('user')->where('id', '=', cy\DB::esc(15));
    }

    public function testConnector() {
        $conn = cy\DB::connector('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\connector\\Mysqli', $conn);
    }

    public function testCompiler() {
        $comp = cy\DB::compiler('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\compiler\\Mysqli', $comp);
    }

    public function testExecutor() {
        $exec = cy\DB::executor('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\executor\\Mysqli', $exec);
    }

    public function testExecutorPrepared() {
        $exec_prep = DB::executor_prepared('cytst-mysqli');
        $this->assertInstanceOf('\\cyclone\\db\\prepared\\executor\\Mysqli', $exec_prep);
    }

}
