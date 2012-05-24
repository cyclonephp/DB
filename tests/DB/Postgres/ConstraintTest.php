<?php

use cyclone\db;
use cyclone as cy;

class DB_Postgres_ConstraintTest extends DB_Postgres_DbTest {

    /**
     * @dataProvider provider_constraint_exceptions
     */
    public function test_constraint_exceptions($err_str, $expected_params) {
        $ex = db\executor\PostgresExceptionBuilder::for_error($err_str);
        foreach ($expected_params as $k => $v) {
            $this->assertEquals($v, $ex->$k);
        }
    }
    
    public function provider_constraint_exceptions() {
        return array(
            array('ERROR: null value in column "notnull_unnamed" violates not-null constraint'
                , array(
                    'constraint_type' => db\ConstraintException::NOTNULL_CONSTRAINT,
                    'column' => 'notnull_unnamed'
                )),
            array('ERROR:  duplicate key value violates unique constraint "chk_constraints_uniq_unnamed_key"
DETAIL:  Key (uniq_unnamed)=(1) already exists.', array(
                    'constraint_type' => db\ConstraintException::UNIQUE_CONSTRAINT,
                    'constraint_name' => 'chk_constraints_uniq_unnamed_key',
                    'column' => 'uniq_unnamed'
                )),
            array(
                'ERROR:  insert or update on table "chk_constraints" violates foreign key constraint "chk_constraints_fk_fkey"
DETAIL:  Key (fk)=(3) is not present in table "users".', array(
                    'constraint_type' => db\ConstraintException::FOREIGNKEY_CONSTRAINT,
                    'constraint_name' => 'chk_constraints_fk_fkey',
                    'column' => 'fk'
                )),
            array(
                'ERROR:  duplicate key value violates unique constraint "uniq_constr"
DETAIL:  Key (uniq_named)=(5) already exists.', array(
                    'constraint_type' => db\ConstraintException::UNIQUE_CONSTRAINT,
                    'constraint_name' => 'uniq_constr',
                    'column' => 'uniq_named'
                )),
            array(
                'ERROR:  new row for relation "chk_constraints" violates check constraint "chk_constraints_chk_unnamed_check"',
                array(
                    'constraint_type' => db\ConstraintException::APP_CONSTRAINT,
                    'constraint_name' => 'chk_constraints_chk_unnamed_check',
            ))

        );
    }

    public function test_unique_constraint_exception() {
        $thrown = FALSE;
        try {
            cy\DB::insert('users')
                ->values(array('name' => 'u'))
                ->values(array('name' => 'u'))->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals('users_name_key', $ex->constraint_name);
            $this->assertEquals(db\ConstraintException::UNIQUE_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');
    }

    public function test_not_null_constraint_exception() {
        $thrown = FALSE;
        $query = cy\DB::update('users')->values(array('name' => null));
        try {
            $query->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::NOTNULL_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');

        try {
            $query->prepare('cytst-postgres')->exec();
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::NOTNULL_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException for prepared stmt thrown');
    }

    public function test_foreign_key_constraint_exception() {
        $thrown = FALSE;
        $query = cy\DB::insert('user_emails')->values(array(
            'user_fk' => 5,
            'email' => 'somebody@example.org'
        ));
        try {
            $query->exec('cytst-postgres');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::FOREIGNKEY_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('user_fk', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');

        try {
            $query->prepare('cytst-postgres')->exec();
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::FOREIGNKEY_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('user_fk', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException for prepared stmt thrown');
    }

}