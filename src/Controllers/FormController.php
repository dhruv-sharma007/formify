<?php

namespace Dhruv\Project\Controllers;

use \Dhruv\Project\Repository\FormRepository;

final class FormController
{
  private FormRepository $formRepository;

  private $question_types = ["single_input" => ["short_answer", "paragraph"], "options" => "multiple_choice", "checkbox"];

  public function __construct()
  {
    $this->formRepository = new FormRepository();
  }

  /**
   * Create a form with questions and options
   * 
   * @param int $userId The user creating the form
   * @param string $title Form title
   * @param string $description Form description
   * @param array $questions Array of questions with their options
   * @return int The created form ID
   * @throws \Exception If creation fails
   */
  public function createForm(string $title, string $description, array $questions, bool $isPublished): int
  {
    // Validate input
    $userId = $_SESSION["user_id"];
    if (!$userId) {
      throw new \Exception("Unauthorize request");
    }
    $this->validateFormInput($userId, $title, $description, $questions);

    return $this->formRepository->createFormWithQuestions($userId, $title, $description, $questions, $isPublished);
  }

  /**
   * Validate form creation input
   * 
   * @param int $userId
   * @param string $title
   * @param string $description
   * @param array $questions
   * @throws \InvalidArgumentException If validation fails
   */
  private function validateFormInput(int $userId, string $title, string $description, array $questions): void
  {
    // Validate user ID
    if ($userId <= 0) {
      throw new \InvalidArgumentException('Invalid user ID');
    }

    // Validate title
    $title = trim($title);
    if (empty($title)) {
      throw new \InvalidArgumentException('Form title is required');
    }
    if (strlen($title) > 255) {
      throw new \InvalidArgumentException('Form title must not exceed 255 characters');
    }

    // Validate description
    if (strlen($description) > 1000) {
      throw new \InvalidArgumentException('Form description must not exceed 1000 characters');
    }

    // Validate questions array
    if (empty($questions)) {
      throw new \InvalidArgumentException('At least one question is required');
    }

    // Define valid question types
    $validTypes = ['short_answer', 'paragraph', 'multiple_choice', 'checkbox'];
    $typesRequiringOptions = ['multiple_choice', 'checkbox'];

    // Validate each question
    foreach ($questions as $index => $question) {
      $questionNum = $index + 1;

      // Check required fields
      if (!isset($question['type'])) {
        throw new \InvalidArgumentException("Question #{$questionNum}: 'type' is required");
      }
      if (!isset($question['question_text'])) {
        throw new \InvalidArgumentException("Question #{$questionNum}: 'question_text' is required");
      }
      if (!isset($question['position'])) {
        throw new \InvalidArgumentException("Question #{$questionNum}: 'position' is required");
      }

      // Validate question type
      if (!in_array($question['type'], $validTypes)) {
        throw new \InvalidArgumentException(
          "Question #{$questionNum}: Invalid question type '{$question['type']}'. Valid types: " . implode(', ', $validTypes)
        );
      }

      // Validate question text
      $questionText = trim($question['question_text']);
      if (empty($questionText)) {
        throw new \InvalidArgumentException("Question #{$questionNum}: Question text cannot be empty");
      }
      if (strlen($questionText) > 500) {
        throw new \InvalidArgumentException("Question #{$questionNum}: Question text must not exceed 500 characters");
      }

      // Validate position
      if (!is_int($question['position']) || $question['position'] <= 0) {
        throw new \InvalidArgumentException("Question #{$questionNum}: Position must be a positive integer");
      }

      // Validate is_required if provided
      if (isset($question['is_required']) && !is_bool($question['is_required'])) {
        throw new \InvalidArgumentException("Question #{$questionNum}: 'is_required' must be a boolean");
      }

      // Validate options based on question type
      if (in_array($question['type'], $typesRequiringOptions)) {
        // Questions that require options
        if (!isset($question['options']) || !is_array($question['options'])) {
          throw new \InvalidArgumentException(
            "Question #{$questionNum}: '{$question['type']}' type requires an 'options' array"
          );
        }
        if (count($question['options']) < 2) {
          throw new \InvalidArgumentException(
            "Question #{$questionNum}: '{$question['type']}' type requires at least 2 options"
          );
        }

        // Validate each option
        foreach ($question['options'] as $optIndex => $optionText) {
          $optionNum = $optIndex + 1;
          $optionText = trim($optionText);

          if (empty($optionText)) {
            throw new \InvalidArgumentException(
              "Question #{$questionNum}, Option #{$optionNum}: Option text cannot be empty"
            );
          }
          if (strlen($optionText) > 150) {
            throw new \InvalidArgumentException(
              "Question #{$questionNum}, Option #{$optionNum}: Option text must not exceed 150 characters"
            );
          }
        }
      } else {
        // Questions that should not have options
        if (isset($question['options']) && !empty($question['options'])) {
          throw new \InvalidArgumentException(
            "Question #{$questionNum}: '{$question['type']}' type should not have options"
          );
        }
      }
    }
  }
}