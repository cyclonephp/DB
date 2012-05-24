<?php

use cyclone\db;
use cyclone as cy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DB_Postgres_SchemaExceptionTest extends Kohana_Unittest_TestCase {

    /**
     * @dataProvider provider_schema_exceptions
     */
    public function test_schema_exceptions($err_str, $expected_params) {
        $ex = db\executor\PostgresExceptionBuilder::for_error($err_str);
        $this->assertEquals($expected_params['class'], get_class($ex)
            , $expected_params['class'] . ' instance is created');
        foreach ($expected_params as $k => $v) {
            if ($k != 'class') {
                $this->assertEquals($v, $ex->$k);
            }
        }
    }

    public function provider_schema_exceptions() {
        return array(
            array('ERROR:  relation "dummy" does not exist
LINE 1: insert into dummy values (\'x\', \'d\');'
                , array(
                'class' => 'cyclone\\db\\SchemaRelationException',
                'relation' => 'dummy'
            )),
            array(
                'ERROR:  column "x" of relation "users" does not exist
LINE 1: insert into users (id, x) values (\'x\', \'d\');', array(
                'class' => 'cyclone\\db\\SchemaColumnException',
                'relation' => 'users',
                'column' => 'x'
            )),
            array(
                'ERROR:  function dummy(integer) does not exist
LINE 1: select dummy(id) from users;', array(
                'class' => 'cyclone\\db\\SchemaFunctionException',
                'function' => 'dummy'
            ))
        );
    }



}
