<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    
  
    
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
    
    // add

    'providers' => [
        'users'=>[
            'driver'=>'eloquent',
            'model'=>\App\Models\User::class
        ]
    ]

];
