<?php

namespace Dhruv\Project\Repository;

use Dhruv\Project\Database\Connection;
use PDO;

class AnswerRepository
{
  private PDO $db;

  public function __construct()
  {
    $this->db = new Connection()::get();
  }

  public function create(array $data): int
  {
    $sql = "INSERT INTO answers (question_id, content, created_at) VALUES (:question_id, :content, :created_at)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      ':question_id' => $data['question_id'],
      ':content' => $data['content'],
      ':created_at' => date('Y-m-d H:i:s')
    ]);
    return $this->db->lastInsertId();
  }

  public function read(int $id): ?array
  {
    $sql = "SELECT * FROM answers WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
  }

  public function update(int $id, array $data): bool
  {
    $sql = "UPDATE answers SET content = :content, updated_at = :updated_at WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      ':id' => $id,
      ':content' => $data['content'],
      ':updated_at' => date('Y-m-d H:i:s')
    ]);
  }

  public function delete(int $id): bool
  {
    $sql = "DELETE FROM answers WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id]);
  }

  public function getByQuestionId(int $questionId): array
  {
    $sql = "SELECT * FROM answers WHERE question_id = :question_id ORDER BY created_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':question_id' => $questionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}