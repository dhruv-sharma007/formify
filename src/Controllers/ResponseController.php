<?php

namespace Dhruv\Project\Controllers;

use Dhruv\Project\Repository\ResponseRepository;
use Dhruv\Project\Repository\FormRepository;

final class ResponseController
{
  private ResponseRepository $responseRepository;
  private FormRepository $formRepository;

  public function __construct()
  {
    $this->responseRepository = new ResponseRepository();
    $this->formRepository = new FormRepository();
  }

  /**
   * Submit a form response
   * 
   * @param int $formId The form ID
   * @param array $answers Raw answers from POST data
   * @return int The response ID
   * @throws \Exception If submission fails
   */
  public function submitResponse(int $formId, array $rawAnswers): int
  {
    // Validate form exists and is published
    $form = $this->formRepository->getFormByIdWithQuestionsAndOptions($formId);

    if (!$form) {
      throw new \InvalidArgumentException('Form not found');
    }

    if (!$form['is_published']) {
      throw new \InvalidArgumentException('This form is not accepting responses');
    }

    // Get client info
    $ipAddress = $this->getClientIp();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    // Transform and validate answers
    $answers = $this->processAnswers($form['questions'], $rawAnswers);

    return $this->responseRepository->createResponseWithAnswers(
      $formId,
      $ipAddress,
      $userAgent,
      $answers
    );
  }

  /**
   * Process and validate submitted answers
   * 
   * @param array $questions Form questions with options
   * @param array $rawAnswers Raw submitted answers
   * @return array Processed answers ready for database
   * @throws \InvalidArgumentException If validation fails
   */
  private function processAnswers(array $questions, array $rawAnswers): array
  {
    $answers = [];

    foreach ($questions as $question) {
      $questionId = $question['id'];
      $type = $question['type'];
      $isRequired = (bool) $question['is_required'];

      // Check if answer exists for this question
      $rawAnswer = $rawAnswers[$questionId] ?? null;

      // Validate required questions
      if ($isRequired && ($rawAnswer === null || $rawAnswer === '' || (is_array($rawAnswer) && empty($rawAnswer)))) {
        throw new \InvalidArgumentException(
          "Question '{$question['question_text']}' is required"
        );
      }

      // Skip if no answer and not required
      if ($rawAnswer === null || $rawAnswer === '') {
        continue;
      }

      // Process based on question type
      switch ($type) {
        case 'short_answer':
        case 'paragraph':
          // Text-based answers
          $answers[] = [
            'question_id' => $questionId,
            'answer_text' => trim($rawAnswer),
            'option_id' => 0,
          ];
          break;

        case 'multiple_choice':
          // Single option selection
          $optionId = (int) $rawAnswer;
          if (!$this->isValidOption($optionId, $question['options'])) {
            throw new \InvalidArgumentException(
              "Invalid option selected for question '{$question['question_text']}'"
            );
          }
          $answers[] = [
            'question_id' => $questionId,
            'answer_text' => '',
            'option_id' => $optionId,
          ];
          break;

        case 'checkbox':
          // Multiple option selections
          if (!is_array($rawAnswer)) {
            $rawAnswer = [$rawAnswer];
          }
          foreach ($rawAnswer as $optionId) {
            $optionId = (int) $optionId;
            if (!$this->isValidOption($optionId, $question['options'])) {
              throw new \InvalidArgumentException(
                "Invalid option selected for question '{$question['question_text']}'"
              );
            }
            $answers[] = [
              'question_id' => $questionId,
              'answer_text' => '',
              'option_id' => $optionId,
            ];
          }
          break;
      }
    }

    return $answers;
  }

  /**
   * Check if option ID is valid for given options
   */
  private function isValidOption(int $optionId, array $options): bool
  {
    foreach ($options as $option) {
      if ((int) $option['id'] === $optionId) {
        return true;
      }
    }
    return false;
  }

  /**
   * Get client IP address
   */
  private function getClientIp(): string
  {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  }

  /**
   * Get all responses for a form (for analytics)
   */
  public function getFormResponses(int $formId): array
  {
    return $this->responseRepository->getResponsesByFormId($formId);
  }

  /**
   * Get a single response with details
   */
  public function getResponse(int $responseId): ?array
  {
    return $this->responseRepository->getResponseWithAnswers($responseId);
  }

  /**
   * Get response count for a form
   */
  public function getResponseCount(int $formId): int
  {
    return $this->responseRepository->getResponseCount($formId);
  }

  /**
   * Get full analytics data for a form
   * 
   * @param int $formId
   * @return array Analytics data including summary and question breakdowns
   */
  public function getFormAnalytics(int $formId): array
  {
    // Get form with questions
    $form = $this->formRepository->getFormByIdWithQuestionsAndOptions($formId);

    if (!$form) {
      throw new \InvalidArgumentException('Form not found');
    }

    // Get response count
    $totalResponses = $this->responseRepository->getResponseCount($formId);

    // Get option analytics for multiple choice/checkbox questions
    $optionStats = $this->responseRepository->getOptionAnalytics($formId);

    // Group option stats by question
    $questionStats = [];
    foreach ($optionStats as $stat) {
      $qId = $stat['question_id'];
      if (!isset($questionStats[$qId])) {
        $questionStats[$qId] = [
          'question_id' => $qId,
          'question_text' => $stat['question_text'],
          'type' => $stat['type'],
          'options' => [],
          'total_answers' => 0,
        ];
      }
      if ($stat['option_id']) {
        $questionStats[$qId]['options'][] = [
          'option_id' => $stat['option_id'],
          'option_text' => $stat['option_text'],
          'count' => (int) $stat['count'],
        ];
        $questionStats[$qId]['total_answers'] += (int) $stat['count'];
      }
    }

    // Get text answers for short_answer and paragraph questions
    foreach ($form['questions'] as $question) {
      if (in_array($question['type'], ['short_answer', 'paragraph'])) {
        $textAnswers = $this->responseRepository->getTextAnswers($question['id']);
        $questionStats[$question['id']] = [
          'question_id' => $question['id'],
          'question_text' => $question['question_text'],
          'type' => $question['type'],
          'answers' => $textAnswers,
          'total_answers' => count($textAnswers),
        ];
      }
    }

    // Get response timeline
    $timeline = $this->responseRepository->getResponseTimeline($formId);

    return [
      'form' => $form,
      'total_responses' => $totalResponses,
      'question_stats' => $questionStats,
      'timeline' => $timeline,
    ];
  }
}
