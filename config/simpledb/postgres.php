<?php

return array(
    'adapter' => 'postgres',
    'connection' => array(
        'host' => 'localhost',
        'port' => 5432,
        'dbname' => 'simpledb',
        'user' => 'simpledb',
        'password' => 'simpledb',
        'persistent' => TRUE
    ),
    'pk_generator_sequences' => array(
        'users' => 'seq_users'
    )
);