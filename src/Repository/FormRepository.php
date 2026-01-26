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

  private function getAll(): array
  {
    // get all forms
    $stmt = $this->db->query('SELECT id, title, is_published FROM forms ORDER BY created_at DESC');
    return $stmt->fetchAll();
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
  public function createFormWithQuestions(int $userId, string $title, ?string $description, array $questions, bool $isPublished = false): int
  {
    try {
      // Start transaction
      $this->db->beginTransaction();

      // 1. Insert form
      $formStmt = $this->db->prepare(
        'INSERT INTO forms (user_id, title, description, is_published, created_at, updated_at)
         VALUES (:user_id, :title, :description, :is_published, NOW(), NOW())'
      );

      $formStmt->execute([
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
        'is_published' => $isPublished ? 1 : 0
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

  /**
   * Get Forms Array by user id
   * @param string $userId
   * @return array
   *
   * [
   *  {
   *    id: 2323,
   *    title: "some title",
   *    responses: 3
   *  },
   *  {
   *    id: 2323,
   *    title: "some title",
   *    responses: 3
   *  }
   * ]
   */

  public function getFormsByUserId(string $userId): array
  {
    $stmt = $this->db->prepare(
      'SELECT id, title, (SELECT COUNT(*) FROM responses WHERE form_id = forms.id) as responses FROM forms WHERE user_id = :user_id ORDER BY created_at DESC'
    );

    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
  }

  /**
   * Gets form with questions and options by form ID
   * 
   * @param int $formId
   * @return array|null Returns form data with questions and options, or null if not found
   */
  public function getFormByIdWithQuestionsAndOptions(int $formId): ?array
  {
    // Get form
    $formStmt = $this->db->prepare(
      'SELECT id, user_id, title, description, is_published, created_at, updated_at
       FROM forms WHERE id = :form_id'
    );
    $formStmt->execute(['form_id' => $formId]);
    $form = $formStmt->fetch(PDO::FETCH_ASSOC);

    if (!$form) {
      return null;
    }

    // Get questions
    $questionStmt = $this->db->prepare(
      'SELECT id, type, question_text, is_required, position
       FROM questions WHERE form_id = :form_id ORDER BY position ASC'
    );
    $questionStmt->execute(['form_id' => $formId]);
    $questions = $questionStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get options for each question
    foreach ($questions as &$question) {
      $optionStmt = $this->db->prepare(
        'SELECT id, option_text, position
         FROM options WHERE question_id = :question_id ORDER BY position ASC'
      );
      $optionStmt->execute(['question_id' => $question['id']]);
      $options = $optionStmt->fetchAll(PDO::FETCH_ASSOC);
      $question['options'] = $options;
    }

    $form['questions'] = $questions;
    return $form;
  }

  public function deleteFormById(int $formId): bool
  {
    try {
      $this->db->beginTransaction();

      $stmt = $this->db->prepare('DELETE FROM forms WHERE id = :form_id');
      $result = $stmt->execute(['form_id' => $formId]);

      $this->db->commit();

      return $result;

    } catch (\Exception $e) {
      $this->db->rollBack();
      throw new \Exception('Failed to delete form: ' . $e->getMessage());
    }
  }
}