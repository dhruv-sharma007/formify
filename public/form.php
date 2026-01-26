<?php

require_once __DIR__ . '/../src/bootstrap.php';

use Dhruv\Project\Controllers\FormController;

$pageTitle = "Fill Form";
$showNavbar = false;

// Get form ID from URL
$formId = isset($_GET['formId']) ? (int) $_GET['formId'] : 0;

if ($formId <= 0) {
  http_response_code(400);
  exit('Invalid form ID');
}

try {
  $form = (new FormController())->getForm($formId);

  if (!$form) {
    http_response_code(404);
    exit('Form not found');
  }

  if (!$form['is_published']) {
    http_response_code(403);
    exit('This form is not accepting responses');
  }

  $pageTitle = htmlspecialchars($form['title']);
} catch (\Exception $e) {
  http_response_code(500);
  exit('Error loading form: ' . $e->getMessage());
}

require_once __DIR__ . '/layouts/header.php';
?>

<main class="max-w-xl mx-auto p-6 space-y-6">
  <div class="mb-8">
    <h1 class="text-2xl font-semibold"><?= htmlspecialchars($form['title']) ?></h1>
    <?php if (!empty($form['description'])): ?>
      <p class="text-base-content/70 mt-2"><?= htmlspecialchars($form['description']) ?></p>
    <?php endif; ?>
  </div>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <span><?= htmlspecialchars($_GET['error']) ?></span>
    </div>
  <?php endif; ?>

  <form method="POST" action="action.php?action=submit-response" class="space-y-6">
    <input type="hidden" name="form_id" value="<?= $form['id'] ?>">

    <?php foreach ($form['questions'] as $index => $question): ?>
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <label class="label">
            <span class="label-text text-lg">
              <?= htmlspecialchars($question['question_text']) ?>
              <?php if ($question['is_required']): ?>
                <span class="text-error">*</span>
              <?php endif; ?>
            </span>
          </label>

          <?php
          $inputName = "answers[{$question['id']}]";
          $isRequired = $question['is_required'] ? 'required' : '';
          ?>

          <?php if ($question['type'] === 'short_answer'): ?>
            <!-- Short Answer Input -->
            <input type="text" name="<?= $inputName ?>" class="input input-bordered w-full" placeholder="Your answer"
              <?= $isRequired ?>>

          <?php elseif ($question['type'] === 'paragraph'): ?>
            <!-- Paragraph Textarea -->
            <textarea name="<?= $inputName ?>" class="textarea textarea-bordered w-full h-32" placeholder="Your answer"
              <?= $isRequired ?>></textarea>

          <?php elseif ($question['type'] === 'multiple_choice'): ?>
            <!-- Multiple Choice (Radio) -->
            <div class="space-y-2">
              <?php foreach ($question['options'] as $option): ?>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="radio" name="<?= $inputName ?>" value="<?= $option['id'] ?>" class="radio radio-primary"
                    <?= $isRequired ?>>
                  <span><?= htmlspecialchars($option['option_text']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>

          <?php elseif ($question['type'] === 'checkbox'): ?>
            <!-- Checkbox (Multiple Select) -->
            <div class="space-y-2">
              <?php foreach ($question['options'] as $option): ?>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" name="<?= $inputName ?>[]" value="<?= $option['id'] ?>"
                    class="checkbox checkbox-primary">
                  <span><?= htmlspecialchars($option['option_text']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>

          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary w-full">Submit</button>
  </form>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>