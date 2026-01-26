<?php
require_once __DIR__ . '/../src/bootstrap.php';

use Dhruv\Project\Controllers\FormController;

$pageTitle = "Success";
$showNavbar = false;
$bodyClass = "min-h-screen flex items-center justify-center bg-base-200";

// Get form details if form_id is provided
$formTitle = "Form";
if (isset($_GET['form_id'])) {
  $formId = (int) $_GET['form_id'];
  try {
    $form = (new FormController())->getForm($formId);
    if ($form) {
      $formTitle = htmlspecialchars($form['title']);
    }
  } catch (\Exception $e) {
    // Ignore errors, use default title
  }
}

require_once __DIR__ . '/layouts/header.php';
?>

<div class="card bg-base-100 shadow-xl p-8 text-center max-w-md">
  <div class="mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-success mx-auto" fill="none" viewBox="0 0 24 24"
      stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  </div>
  <h2 class="text-2xl font-semibold text-success">Response Submitted!</h2>
  <p class="opacity-70 mt-3">Thank you for completing <strong><?= $formTitle ?></strong>.</p>
  <p class="opacity-50 text-sm mt-2">Your response has been recorded.</p>

  <div class="mt-6">
    <a href="/" class="btn btn-outline btn-sm">Back to Home</a>
  </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>