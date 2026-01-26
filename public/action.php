<?php

use Dhruv\Project\Controllers\AuthController;
use Dhruv\Project\Controllers\FormController;
use Dhruv\Project\Controllers\ResponseController;
require_once __DIR__ . '/../src/bootstrap.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'register':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ((new AuthController())->register($name, $email, $password)) {
                header('Location: login.php');
            } else {
                echo "something went wrong";
            }

            break;

        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ((new AuthController())->login($email, $password)) {
                // Debug: Check if user_id is set
                error_log("Session after login: " . print_r($_SESSION, true));
                header('Location: dashboard.php');
                exit;
            } else {
                echo "something went wrong";
            }
            break;

        case 'logout':
            if ((new AuthController())->logout()) {
                header('Location: /');
            }
            break;

        case 'submit-form':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';

            // Handle questions - can be either JSON string or already an array
            $rawQuestions = $_POST['questions'] ?? [];
            if (is_string($rawQuestions)) {
                $rawQuestions = json_decode($rawQuestions, true) ?: [];
            }

            // Transform question data to expected format
            $questions = [];
            foreach ($rawQuestions as $index => $q) {
                $question = [
                    'type' => $q['type'] ?? 'short_answer',
                    'question_text' => $q['text'] ?? '',
                    'is_required' => isset($q['required']) ? (bool) $q['required'] : false,
                    'position' => isset($q['position']) ? (int) $q['position'] + 1 : $index + 1,
                ];

                // Add options if present
                if (isset($q['options']) && is_array($q['options'])) {
                    $question['options'] = $q['options'];
                }

                $questions[] = $question;
            }

            $formId = (new FormController())->createForm($title, $description, $questions);
            header('Location: dashboard.php?success=Form created successfully');
            exit;
            break;

        case 'submit-response':
            $formId = isset($_POST['form_id']) ? (int) $_POST['form_id'] : 0;
            $answers = $_POST['answers'] ?? [];

            if ($formId <= 0) {
                throw new \InvalidArgumentException('Invalid form ID');
            }

            $responseId = (new ResponseController())->submitResponse($formId, $answers);
            header('Location: success.php?form_id=' . $formId);
            exit;
            break;

    }
} catch (\Throwable $th) {
    // For submit-response, redirect back to form with error
    if (($action ?? '') === 'submit-response' && isset($formId) && $formId > 0) {
        header('Location: form.php?formId=' . $formId . '&error=' . urlencode($th->getMessage()));
        exit;
    }
    echo json_encode(['error' => $th->getMessage()]);
}
