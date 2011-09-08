<?php

namespace cyclone\db\prepared;

/**
 * @author Bence Eros <crystal@cyclonephp.com>
 * @package DB
 */
interface Query {

    public function param($value, $key = '?');

    public function params(array $params);

    public function exec();
    
}
