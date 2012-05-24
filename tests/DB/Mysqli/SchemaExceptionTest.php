<?php

use cyclone as cy;
use cyclone\db;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DB_Mysqli_SchemaExceptionTest extends Kohana_Unittest_TestCase {

    /**
     * @dataProvider provider_schema_exceptions
     */
    public function test_schema_exceptions($err_str, $errno, $expected_params) {
        $ex = db\executor\MysqlExceptionBuilder::for_error($err_str, $errno);
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
            array('Table \'mydb.cy_use\' doesn\'t exist'
                , 1146
                , array(
                    'class' => 'cyclone\\db\\SchemaRelationException',
                    'relation' => 'cy_use'
            )),
            array(
                'Unknown column \'x\' in \'field list\''
                , 1054
                , array(
                    'class' => 'cyclone\\db\\SchemaColumnException',
                    //'relation' => 'users',
                    'column' => 'x'
            ))
            , array(
                'FUNCTION mydb.hello does not exist'
                , 1305
                , array(
                    'class' => 'cyclone\\db\\SchemaFunctionException',
                    'function' => 'hello'
            ))
        );
    }
}
