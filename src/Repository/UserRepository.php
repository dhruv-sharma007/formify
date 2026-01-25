<?php

namespace Dhruv\Project\Repository;

use Dhruv\Project\Database\Connection;
use PDO;

final class UserRepository
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Connection::get();
  }

  public function create(string $name, string $email, string $password): bool
  {
    $stmt = $this->db->prepare(
      'INSERT INTO users (name, email, password, created_at, updated_at)
         VALUES (:user_name, :email, :password, NOW(), NOW())'
    );

    return $stmt->execute([
      'user_name' => $name,
      'email' => $email,
      'password' => $password,
    ]);
  }

  public function findById(int $userId): ?array
  {
    $stmt = $this->db->prepare('SELECT name, email, isVerified from users WHERE id = :user_id');

    $stmt->execute([
      'user_id' => $userId
    ]);
    $stmt->fetch() ?: null;
  }

  public function findByEmail(string $email): ?array
  {
    $stmt = $this->db->prepare(
      'SELECT name, email, isVerified FROM users WHERE email = :email'
    );

    $stmt->execute([
      'email' => $email
    ]);

    return $stmt->fetch() ?: null;
  }

  public function isPasswordValid(string $password, string $email): bool
  {
    $stmt = $this->db->prepare(
      'SELECT password FROM users WHERE email = :email'
    );

    $stmt->execute([
      'email' => $email
    ]);

    $user = $stmt->fetch() ?: null;

    return password_verify($password, $user['password']);
  }

  public function getAll(): array
  {
    // get all forms
    $stmt = $this->db->query('SELECT id, title, is_published FROM forms ORDER BY created_at DESC');
    return $stmt->fetchAll();
  }

}