<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value, the allowed methods however have to be explicitly listed.
     |
     */

    'defaults' => array(
        'supportsCredentials' => true,
        'allowedOrigins' => array('auth.lapinedaback.app'),
        'allowedHeaders' => array('Origin', 'X-Requested-With', 'Content-Type', 'Accept'),
        'allowedMethods' => array('POST', 'PUT', 'GET', 'DELETE','OPTIONS'),
        'exposedHeaders' => array('*'),
        'maxAge' => 3600,
        'hosts' => array(),
    ),

   'paths' => array(
        'api/*' => array(
            'allowedOrigins' => array('*'),
            'allowedHeaders' => array('Origin', 'X-Requested-With', 'Content-Type', 'Accept'),
            'allowedMethods' =>  array('POST', 'PUT', 'GET', 'DELETE','OPTIONS'),
            'maxAge' => 3600,
        ),
        '*' => array(
            'allowedOrigins' => array('*'),
            'allowedHeaders' => array('Origin', 'X-Requested-With', 'Content-Type', 'Accept'),
            'allowedMethods' =>  array('POST', 'PUT', 'GET', 'DELETE','OPTIONS'),
            'maxAge' => 3600,
            'hosts' => array('*'),
        ),
    ),
];

