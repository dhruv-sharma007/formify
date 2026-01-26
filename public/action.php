<?php

use Dhruv\Project\Controllers\AuthController;
use Dhruv\Project\Controllers\FormController;
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
                header('Location: dashboard.php');
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
            $title = $_POST['title'];
            $description = $_POST['description'];

            //TODO: add publish button and functionality
            $isPublished = true;
            $questions = $_POST['questions'];
            (new FormController())->createForm($title, $description, $questions, true);
            header('Location: dashboard.php');
            // print_r($);
            break;

    }
} catch (\Throwable $th) {
    echo json_encode(['error' => $th->getMessage()]);
}
