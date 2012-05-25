<?php

use cyclone as cy;
use cyclone\db;

require_once __DIR__ . '/DBTest.php';

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DB_Mysqli_ConstraintTest extends DB_Mysqli_DbTest {

    /**
     * @dataProvider provider_constraint_exceptions
     */
    public function test_constraint_exceptions($err_str, $errno, $expected_params) {
        $ex = db\executor\MysqlExceptionBuilder::for_error($err_str, $errno);
        $this->assertEquals('cyclone\\db\\ConstraintException', get_class($ex)
            , 'ConstraintException instance is created');
        foreach ($expected_params as $k => $v) {
            if ($k != 'class') {
                $this->assertEquals($v, $ex->$k);
            }
        }
    }

    public function provider_constraint_exceptions() {
        return array(
            array('Column \'name\' cannot be null'
            , 1048
            , array(
                'constraint_type' => db\ConstraintException::NOTNULL_CONSTRAINT,
                'column' => 'name'
            )),
            array('Duplicate entry \'u@e\' for key \'email\''
            , 1062
            , array(
                'constraint_type' => db\ConstraintException::UNIQUE_CONSTRAINT,
                'constraint_name' => NULL,
                'column' => 'email'
            )),
            array(
                'Cannot add or update a child row: a foreign key constraint fails '
                    . '(`simpledb`.`t_posts`, CONSTRAINT `t_posts_ibfk_1` FOREIGN KEY (`user_fk`)'
                    . ' REFERENCES `cy_user` (`id`))'
                , 1452
                , array(
                'constraint_type' => db\ConstraintException::FOREIGNKEY_CONSTRAINT,
                'constraint_name' => 't_posts_ibfk_1',
                'column' => 'user_fk'
            ))/*,
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
                ))*/

        );
    }

    public function test_unique_constraint_exception() {
        $thrown = FALSE;
        try {
            cy\DB::insert('user_email')
                ->values(array(
                    'user_fk' => 1,
                    'email' => 'user@example.org'))
                ->values(array(
                    'user_fk' => 2,
                    'email' => 'user@example.org'
                ))->exec('cytst-mysqli');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::UNIQUE_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('email', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');
    }

    public function test_not_null_constraint_exception() {
        $thrown = FALSE;
        $query = cy\DB::insert('user')->values(array('name' => null));
        try {
            $query->exec('cytst-mysqli');
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::NOTNULL_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException thrown');

        try {
            $query->prepare('cytst-mysqli')->exec();
        } catch (db\ConstraintException $ex) {
            $this->assertEquals(db\ConstraintException::NOTNULL_CONSTRAINT, $ex->constraint_type);
            $this->assertEquals('name', $ex->column);
            $thrown = TRUE;
        }
        $this->assertTrue($thrown, 'ConstraintException for prepared stmt thrown');
    }

}
