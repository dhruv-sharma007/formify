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

  /**
   * Create a form with questions and options in a transaction
   * 
   * @param int $userId The user creating the form
   * @param string $title Form title
   * @param string|null $description Form description
   * @param array $questions Array of questions with structure:
   *   [
   *     ['type' => 'multiple_choice', 'question_text' => '...', 'is_required' => true, 'position' => 1, 'options' => ['Option 1', 'Option 2']],
   *     ['type' => 'short_answer', 'question_text' => '...', 'is_required' => false, 'position' => 2]
   *   ]
   * @return int The created form ID
   * @throws \Exception If transaction fails
   */
  public function createFormWithQuestions(int $userId, string $title, ?string $description, array $questions): int
  {
    try {
      // Start transaction
      $this->db->beginTransaction();

      // 1. Insert form
      $formStmt = $this->db->prepare(
        'INSERT INTO forms (user_id, title, description, is_published, created_at, updated_at)
         VALUES (:user_id, :title, :description, 0, NOW(), NOW())'
      );

      $formStmt->execute([
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
      ]);

      $formId = (int) $this->db->lastInsertId();

      // 2. Insert questions and their options
      $questionStmt = $this->db->prepare(
        'INSERT INTO questions (form_id, type, question_text, is_required, position, created_at)
         VALUES (:form_id, :type, :question_text, :is_required, :position, NOW())'
      );

      $optionStmt = $this->db->prepare(
        'INSERT INTO options (question_id, option_text, position)
         VALUES (:question_id, :option_text, :position)'
      );

      foreach ($questions as $question) {
        // Insert question
        $questionStmt->execute([
          'form_id' => $formId,
          'type' => $question['type'],
          'question_text' => $question['question_text'],
          'is_required' => $question['is_required'] ?? false,
          'position' => $question['position'],
        ]);

        $questionId = (int) $this->db->lastInsertId();

        // Insert options if they exist
        if (isset($question['options']) && is_array($question['options'])) {
          foreach ($question['options'] as $index => $optionText) {
            $optionStmt->execute([
              'question_id' => $questionId,
              'option_text' => $optionText,
              'position' => $index + 1,
            ]);
          }
        }
      }

      // Commit transaction
      $this->db->commit();

      return $formId;

    } catch (\Exception $e) {
      // Rollback on error
      $this->db->rollBack();
      throw new \Exception('Failed to create form: ' . $e->getMessage());
    }
  }

}