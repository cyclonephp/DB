<?php

return array(
    'db' => array(
        'description' => 'The DB library is a low-level database abstraction layer for CyclonePHP',
        'commands' => array(
            'generate-schema' => array(
                'description' => "Generates database schema.

Iterates on all classes named Record_*, instantiates each one and creates database schema for them.",
                'arguments' => array(
                    '--namespace' => array(
                        'alias' => '-n',
                        'parameter' => '<namespace>',
                        'descr' => 'AbstractRecord implementations will be searched under the <namespace> namespace. You can also pass a comma-separated list of namespaces.',
                        'required' => false
                    ),
                    '--forced' => array(
                        'alias' => '-f',
                        'parameter' => NULL,
                        'descr' => 'Tables will be dropped before creation'
                    ),
                    '--suppress-execution' => array(
                        'alias' => '-s',
                        'parameter' => NULL,
                        'descr' => 'Prints the generated DDL to stdout and does not execute it'
                    )
                ),
                'callback' => array('cyclone\\db\\schema\\Builder', 'build_schema')
            )
        )
    )
);

