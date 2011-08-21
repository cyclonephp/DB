<?php

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
interface DB_Query_Prepared {

    public function param($value, $key = '?');

    public function params(array $params);

    public function exec();
    
}
