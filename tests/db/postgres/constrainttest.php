<?php

use cyclone\db;

class DB_Postgres_ConstraintTest extends DB_Postgres_DbTest {

    /**
     * @dataProvider providerConstraintExceptions
     */
    public function testConstraintExceptions($err_str, $expected_params) {
        $ex = db\executor\PostgresConstraintExceptionBuilder::for_error($err_str);
        foreach ($expected_params as $k => $v) {
            $this->assertEquals($v, $ex->$k);
        }
    }
    
    private function createConstraintException($params) {
        $rval = new db\ConstraintException;
        foreach ($params as $k => $v) {
            $rval->$k = $v;
        }
        return $rval;
    }

    public function providerConstraintExceptions() {
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

}