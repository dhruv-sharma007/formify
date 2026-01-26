<?php

use Dhruv\Project\Controllers\FormController;
$pageTitle = "Dashboard";
// $isLoggedIn = true;
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/layouts/header.php';
$userId = $_SESSION['user_id'];
$forms = (new FormController())->getForms($userId);
?>

<main class="max-w-6xl mx-auto p-6">
  <div class="flex justify-between mb-6">
    <h1 class="text-xl font-semibold">Your Forms</h1>
    <a href="form-create.php" class="btn btn-primary">+ New Form</a>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    <?php foreach ($forms as $form): ?>
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <h2 class="font-semibold"><?= htmlspecialchars($form['title']) ?></h2>
          <p class="text-sm opacity-70"><?= $form['responses'] ?? 0 ?> responses</p>

          <div class="flex gap-2 mt-4">
            <!-- <a class="btn btn-sm">Edit</a> -->
            <a href="analytics.php?formId=<?= $form['id'] ?>" class="btn btn-sm btn-outline">Analytics</a>
            <button class="btn btn-sm btn-ghost" onclick="copyFormLink(<?= $form['id'] ?>, this)" title="Copy form link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <span class="copy-text">Share Form</span>
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</main>

<script>
  function copyFormLink(formId, button) {
    const url = `${window.location.origin}/form.php?formId=${formId}`;
    const copyText = button.querySelector('.copy-text');

    navigator.clipboard.writeText(url).then(() => {
      // Show success feedback
      const originalText = copyText.textContent;
      copyText.textContent = 'Copied!';
      button.classList.add('btn-success');

      // Reset after 2 seconds
      setTimeout(() => {
        copyText.textContent = originalText;
        button.classList.remove('btn-success');
      }, 2000);
    }).catch(err => {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = url;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);

      copyText.textContent = 'Link Copied!';
      button.classList.add('btn-success');
      setTimeout(() => {
        copyText.textContent = 'Copy Link';
        button.classList.remove('btn-success');
      }, 2000);
    });
  }
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>