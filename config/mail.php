<?php
return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'log' => ['transport' => 'log'],
        'array' => ['transport' => 'array'],
    ],
    'from' => ['address' => env('MAIL_FROM_ADDRESS'), 'name' => env('MAIL_FROM_NAME')],
];
