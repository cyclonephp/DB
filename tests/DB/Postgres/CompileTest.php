<?php

use cyclone as cy;
use cyclone\db;

require_once cy\FileSystem::get_default()->get_root_path('db') . 'tests/DB/Postgres/DBTest.php';

class DB_Postgres_CompileTest extends Kohana_Unittest_TestCase {

    public function testConnection() {
        cy\DB::connector('cytst-postgres')->connect();
    }

    public function testCompileInsert() {
        $query = cy\DB::insert('user')->values(array(
            'name' => 'user'
            , 'email' => 'user@example.com'))->returning('id', 'name', 'email');
        $this->assertEquals('INSERT INTO "user" ("name", "email") VALUES (\'user\', \'user@example.com\')'
                . ' RETURNING "id", "name", "email"'
                , $query->compile('cytst-postgres'));
    }

    public function testCompileUpdate() {
        $query = cy\DB::update('user')->values(array('name' => NULL, 'email' => 'ebence88@gmail.com'))
                ->where('id', '=', DB::esc(1))->limit(10)->returning('name');

        $this->assertEquals('UPDATE "user" SET "name" = NULL, "email" = \'ebence88@gmail.com\' WHERE "id" = \'1\' LIMIT 10'
                . ' RETURNING "name"',
                $query->compile('cytst-postgres'));
    }

    public function testCompileDelete() {
        $query = cy\DB::delete('user')->where('name', 'like', '%crys%')
            ->limit(10)->returning('name');

        $this->assertEquals('DELETE FROM "user" WHERE "name" like "%crys%" LIMIT 10'
                . ' RETURNING "name"'
                , $query->compile('cytst-postgres'));
    }

    /**
     * @expectedException cyclone\db\Exception
     */
    public function testCompileHint() {
        $query = cy\DB::select()->hint('dummy')->from('table');
        $query->compile('cytst-postgres');
    }
    
}