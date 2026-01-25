<?php

namespace Dhruv\Project\Repository;

use Dhruv\Project\Database\Connection;
use PDO;

final class FormRepository
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Connection::get();
  }

  public function getAll(): array
  {
    // get all forms
    $stmt = $this->db->query('SELECT id, title, is_published FROM forms ORDER BY created_at DESC');
    return $stmt->fetchAll();
  }

  public function findById(int $id): ?array
  {
    $stmt = $this->db->prepare('SELECT * FROM forms WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
  }

  public function create(int $userId, string $title, ?string $description): int
  {
    $stmt = $this->db->prepare(
      'INSERT INTO forms (user_id, title, description, is_published, created_at, updated_at)
         VALUES (:user_id, :title, :description, 0, NOW(), NOW())'
    );
     
    $stmt->execute([
      'user_id' => $userId,
      'title' => $title,
      'description' => $description,
    ]);

    return (int) $this->db->lastInsertId();
  }

}