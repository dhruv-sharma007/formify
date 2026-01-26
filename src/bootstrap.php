<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dhruv\Project\Database\Connection;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.gc_maxlifetime', 1800);
  session_start();
}

Connection::get();
