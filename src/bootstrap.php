<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dhruv\Project\Database\Connection;
ini_set('session.gc_maxlifetime', 1800);
session_start();

Connection::get();
