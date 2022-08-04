<?php

$databases = [];
$databases['default']['default'] = array (
  'database' => $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE'),
  'username' => $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER'),
  'password' => $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD'),
  'prefix' => '',
  'host' => $_ENV['MYSQL_HOST'] ?? getenv('MYSQL_HOST'),
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
