<?php
// Session already started in bootstrap.php
require_once __DIR__ . "/../src/bootstrap.php";
$isLoggedIn;

if (!isset($_SESSION['logged_in'])) {
  http_response_code(401);
  $isLoggedIn = false;
  exit('please <a href="/login.php">login</a> first to access this page');
} else {

  $isLoggedIn = true;
}
