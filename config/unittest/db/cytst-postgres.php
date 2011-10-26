<?php

return array(
    'adapter' => 'postgres',
    'connection' => array(
        'host' => 'localhost',
        'port' => 5433,
        'dbname' => 'cyclone',
        'user' => 'cyclone',
        'password' => 'cyclone',
        'persistent' => TRUE
    ),
    'pk_generator_sequences' => array(
        'users' => 'seq_users'
    )
);
