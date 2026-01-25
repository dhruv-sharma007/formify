<?php

return [
  'paths' => [
    'migrations' => 'migrations',
    'seeds' => 'seeds',
  ],

  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_environment' => 'development',

    'development' => [
      'adapter' => 'mysql',
      'host' => '127.0.0.1',
      'name' => 'formapp',
      'user' => 'appuser',
      'pass' => 'root',
      'port' => '3306',
      'charset' => 'utf8mb4',
    ],
  ],
];
