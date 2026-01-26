<?php

namespace Dhruv\Project\Repository;

use Dhruv\Project\Database\Connection;
use PDO;

final class ResponseRepository
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Connection::get();
  }

  /**
   * Create a response with answers in a transaction
   * 
   * @param int $formId The form being responded to
   * @param string $ipAddress Client IP address
   * @param string $userAgent Client user agent
   * @param array $answers Array of answers with structure:
   *   [
   *     ['question_id' => 1, 'answer_text' => 'My answer', 'option_id' => null],
   *     ['question_id' => 2, 'answer_text' => '', 'option_id' => 5],
   *   ]
   * @return int The created response ID
   * @throws \Exception If transaction fails
   */
  public function createResponseWithAnswers(int $formId, string $ipAddress, string $userAgent, array $answers): int
  {
    try {
      $this->db->beginTransaction();

      // 1. Insert response
      $responseStmt = $this->db->prepare(
        'INSERT INTO responses (form_id, ip_address, user_agent, created_at, updated_at)
         VALUES (:form_id, :ip_address, :user_agent, NOW(), NOW())'
      );

      $responseStmt->execute([
        'form_id' => $formId,
        'ip_address' => $ipAddress,
        'user_agent' => $userAgent,
      ]);

      $responseId = (int) $this->db->lastInsertId();

      // 2. Insert answers
      $answerStmt = $this->db->prepare(
        'INSERT INTO answers (response_id, question_id, answerText, option_id)
         VALUES (:response_id, :question_id, :answer_text, :option_id)'
      );

      foreach ($answers as $answer) {
        $answerStmt->execute([
          'response_id' => $responseId,
          'question_id' => $answer['question_id'],
          'answer_text' => $answer['answer_text'] ?? '',
          'option_id' => $answer['option_id'] ?? 0,
        ]);
      }

      $this->db->commit();

      return $responseId;

    } catch (\Exception $e) {
      $this->db->rollBack();
      throw new \Exception('Failed to submit response: ' . $e->getMessage());
    }
  }

  /**
   * Get all responses for a form
   * 
   * @param int $formId
   * @return array
   */
  public function getResponsesByFormId(int $formId): array
  {
    $stmt = $this->db->prepare(
      'SELECT id, form_id, ip_address, user_agent, created_at
       FROM responses WHERE form_id = :form_id ORDER BY created_at DESC'
    );
    $stmt->execute(['form_id' => $formId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get a response with all its answers
   * 
   * @param int $responseId
   * @return array|null
   */
  public function getResponseWithAnswers(int $responseId): ?array
  {
    // Get response
    $responseStmt = $this->db->prepare(
      'SELECT id, form_id, ip_address, user_agent, created_at
       FROM responses WHERE id = :response_id'
    );
    $responseStmt->execute(['response_id' => $responseId]);
    $response = $responseStmt->fetch(PDO::FETCH_ASSOC);

    if (!$response) {
      return null;
    }

    // Get answers with question details
    $answerStmt = $this->db->prepare(
      'SELECT a.id, a.question_id, a.answerText, a.option_id,
              q.question_text, q.type,
              o.option_text
       FROM answers a
       JOIN questions q ON a.question_id = q.id
       LEFT JOIN options o ON a.option_id = o.id
       WHERE a.response_id = :response_id
       ORDER BY q.position ASC'
    );
    $answerStmt->execute(['response_id' => $responseId]);
    $response['answers'] = $answerStmt->fetchAll(PDO::FETCH_ASSOC);

    return $response;
  }

  /**
   * Get response count for a form
   * 
   * @param int $formId
   * @return int
   */
  public function getResponseCount(int $formId): int
  {
    $stmt = $this->db->prepare('SELECT COUNT(*) FROM responses WHERE form_id = :form_id');
    $stmt->execute(['form_id' => $formId]);
    return (int) $stmt->fetchColumn();
  }

  /**
   * Get analytics data for a form - option counts for multiple choice/checkbox
   * 
   * @param int $formId
   * @return array
   */
  public function getOptionAnalytics(int $formId): array
  {
    $stmt = $this->db->prepare(
      'SELECT 
        q.id as question_id,
        q.question_text,
        q.type,
        o.id as option_id,
        o.option_text,
        COUNT(a.id) as count
       FROM questions q
       LEFT JOIN options o ON o.question_id = q.id
       LEFT JOIN answers a ON a.option_id = o.id
       WHERE q.form_id = :form_id AND q.type IN ("multiple_choice", "checkbox")
       GROUP BY q.id, o.id
       ORDER BY q.position ASC, o.position ASC'
    );
    $stmt->execute(['form_id' => $formId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get text answers for a question
   * 
   * @param int $questionId
   * @param int $limit
   * @return array
   */
  public function getTextAnswers(int $questionId, int $limit = 50): array
  {
    $stmt = $this->db->prepare(
      'SELECT a.answerText, r.created_at
       FROM answers a
       JOIN responses r ON a.response_id = r.id
       WHERE a.question_id = :question_id AND a.answerText != ""
       ORDER BY r.created_at DESC
       LIMIT :limit'
    );
    $stmt->bindValue(':question_id', $questionId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get response timeline (responses per day)
   * 
   * @param int $formId
   * @param int $days
   * @return array
   */
  public function getResponseTimeline(int $formId, int $days = 30): array
  {
    $stmt = $this->db->prepare(
      'SELECT DATE(created_at) as date, COUNT(*) as count
       FROM responses
       WHERE form_id = :form_id AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
       GROUP BY DATE(created_at)
       ORDER BY date ASC'
    );
    $stmt->bindValue(':form_id', $formId, PDO::PARAM_INT);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
