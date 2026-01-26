<?php

namespace Dhruv\Project\Controllers;

use Dhruv\Project\Repository\UserRepository;

final class AuthController
{
  private UserRepository $userRepository;

  public function __construct()
  {
    $this->userRepository = new UserRepository();
  }

  public function login(string $email, string $password): bool
  {

    // Receive Login Request => Validate Input => Locate User => Verify Password => Check Account Status => Create Auth Session / Token => Return Response

    if ($email == '' || $password == '') {
      throw new \Exception("all fields are required");
    }

    if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \Exception("Invalid email");
    }

    //does user exist
    if (\strlen($password) < 8) {
      throw new \Exception("password is too short | minimum length is 8");
    }

    $user = $this->userRepository->findByEmail($email);

    if (!$user) {
      throw new \Exception("user does not exist");
    }

    if (!$this->userRepository->isPasswordValid($password, $email)) {
      throw new \Exception("incorrect password: entered password => " . $password . " " . "db password =>" . $user["password"]);
    }

    // TODO: only if magic link implemented
    // if (!$user['isVerified']) {
    //   throw new \Exception("user is not verified");
    // }

    // Session already started in bootstrap.php
    // Regenerate session ID for security
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['logged_in'] = true;
    $_SESSION['email'] = $user['email'];

    return true;
  }


  public function logout(): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();

      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );

    }
    return session_destroy();
  }

  public function register(string $name, string $email, string $password): bool
  {

    // input validation
    if ($name == '' || $email == '' || $password == '') {
      throw new \Exception("all fields are required");
    }

    if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \Exception("Invalid email");
    }

    //does user exist
    if (\strlen($password) < 8) {
      throw new \Exception("password is too short | minimum length is 8");
    }

    $user = $this->userRepository->findByEmail($email);

    if ($user) {
      throw new \Exception("user already exist");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // TODO: implement magic link

    return $this->userRepository->create($name, $email, $hashedPassword);
  }


  // TODO: implement verify user when user click verify account in mail inbox
  public function verifyUser()
  {
    
  }
}