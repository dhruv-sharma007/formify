<?php
require_once __DIR__ . '/../src/bootstrap.php';

use Dhruv\Project\Controllers\ResponseController;

$pageTitle = "Analytics";
require_once __DIR__ . '/auth.php';

// Get form ID
$formId = isset($_GET['formId']) ? (int) $_GET['formId'] : 0;

if ($formId <= 0) {
  http_response_code(400);
  exit('Invalid form ID. <a href="dashboard.php">Go back to dashboard</a>');
}

try {
  $analytics = (new ResponseController())->getFormAnalytics($formId);
  $form = $analytics['form'];
  $pageTitle = "Analytics - " . htmlspecialchars($form['title']);

  // Check if user owns this form
  if ((int) $form['user_id'] !== (int) $_SESSION['user_id']) {
    http_response_code(403);
    exit('You do not have permission to view this form\'s analytics');
  }
} catch (\Exception $e) {
  http_response_code(500);
  exit('Error loading analytics: ' . $e->getMessage());
}

require_once __DIR__ . '/layouts/header.php';
?>

<main class="max-w-5xl mx-auto p-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <div>
      <a href="dashboard.php" class="text-sm opacity-70 hover:opacity-100">← Back to Dashboard</a>
      <h1 class="text-2xl font-semibold mt-2"><?= htmlspecialchars($form['title']) ?></h1>
      <?php if (!empty($form['description'])): ?>
        <p class="text-base-content/70"><?= htmlspecialchars($form['description']) ?></p>
      <?php endif; ?>
    </div>
    <button class="btn btn-ghost btn-sm" onclick="copyFormLink(<?= $form['id'] ?>, this)">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
      </svg>
      <span class="copy-text">Share Form</span>
    </button>
  </div>

  <!-- Summary Stats -->
  <div class="stats shadow bg-base-100 w-full mb-8">
    <div class="stat">
      <div class="stat-figure text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <div class="stat-title">Total Responses</div>
      <div class="stat-value text-primary"><?= $analytics['total_responses'] ?></div>
    </div>

    <div class="stat">
      <div class="stat-figure text-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="stat-title">Questions</div>
      <div class="stat-value text-secondary"><?= count($form['questions']) ?></div>
    </div>

    <div class="stat">
      <div class="stat-figure text-accent">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="stat-title">Status</div>
      <div class="stat-value text-accent text-lg">
        <?= $form['is_published'] ? 'Published' : 'Draft' ?>
      </div>
    </div>
  </div>

  <?php if ($analytics['total_responses'] === 0): ?>
    <!-- No responses yet -->
    <div class="card bg-base-100 shadow">
      <div class="card-body text-center py-12">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto opacity-30" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h3 class="text-lg font-semibold mt-4">No responses yet</h3>
        <p class="opacity-70">Share your form to start collecting responses</p>
      </div>
    </div>
  <?php else: ?>

    <!-- Response Timeline Chart -->
    <?php if (!empty($analytics['timeline'])): ?>
      <div class="card bg-base-100 shadow mb-8">
        <div class="card-body">
          <h2 class="card-title">Responses Over Time</h2>
          <div class="h-48 flex items-end gap-1 mt-4">
            <?php
            $maxCount = max(array_column($analytics['timeline'], 'count'));
            foreach ($analytics['timeline'] as $day):
              $height = $maxCount > 0 ? ($day['count'] / $maxCount) * 100 : 0;
              ?>
              <div class="flex-1 flex flex-col items-center group">
                <div class="text-xs opacity-0 group-hover:opacity-100 mb-1"><?= $day['count'] ?></div>
                <div class="w-full bg-primary rounded-t transition-all hover:bg-primary-focus"
                  style="height: <?= max($height, 5) ?>%" title="<?= $day['date'] ?>: <?= $day['count'] ?> responses"></div>
                <div class="text-xs opacity-50 mt-1 rotate-45 origin-left"><?= date('M j', strtotime($day['date'])) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Question Analytics -->
    <h2 class="text-xl font-semibold mb-4">Question Breakdown</h2>

    <div class="space-y-6">
      <?php foreach ($form['questions'] as $question):
        $stats = $analytics['question_stats'][$question['id']] ?? null;
        ?>
        <div class="card bg-base-100 shadow">
          <div class="card-body">
            <h3 class="card-title text-base">
              <?= htmlspecialchars($question['question_text']) ?>
              <?php if ($question['is_required']): ?>
                <span class="text-error text-sm">*</span>
              <?php endif; ?>
            </h3>
            <span class="badge badge-ghost badge-sm"><?= ucfirst(str_replace('_', ' ', $question['type'])) ?></span>

            <?php if ($question['type'] === 'multiple_choice' || $question['type'] === 'checkbox'): ?>
              <!-- Bar chart for options -->
              <?php if ($stats && !empty($stats['options'])): ?>
                <div class="mt-4 space-y-3">
                  <?php foreach ($stats['options'] as $option):
                    $percentage = $stats['total_answers'] > 0
                      ? round(($option['count'] / $stats['total_answers']) * 100)
                      : 0;
                    ?>
                    <div>
                      <div class="flex justify-between text-sm mb-1">
                        <span><?= htmlspecialchars($option['option_text']) ?></span>
                        <span class="font-medium"><?= $option['count'] ?> (<?= $percentage ?>%)</span>
                      </div>
                      <div class="w-full bg-base-200 rounded-full h-3">
                        <div class="bg-primary h-3 rounded-full transition-all" style="width: <?= $percentage ?>%"></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <p class="text-sm opacity-70 mt-3"><?= $stats['total_answers'] ?> total selections</p>
              <?php else: ?>
                <p class="text-sm opacity-50 mt-2">No responses for this question yet</p>
              <?php endif; ?>

            <?php elseif ($question['type'] === 'short_answer' || $question['type'] === 'paragraph'): ?>
              <!-- Text responses -->
              <?php if ($stats && !empty($stats['answers'])): ?>
                <div class="mt-4 max-h-64 overflow-y-auto space-y-2">
                  <?php foreach ($stats['answers'] as $answer): ?>
                    <div class="bg-base-200 rounded-lg p-3">
                      <p class="text-sm"><?= htmlspecialchars($answer['answerText']) ?></p>
                      <p class="text-xs opacity-50 mt-1"><?= date('M j, Y g:i A', strtotime($answer['created_at'])) ?></p>
                    </div>
                  <?php endforeach; ?>
                </div>
                <p class="text-sm opacity-70 mt-3"><?= $stats['total_answers'] ?> responses</p>
              <?php else: ?>
                <p class="text-sm opacity-50 mt-2">No responses for this question yet</p>
              <?php endif; ?>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</main>

<script>
  function copyFormLink(formId, button) {
    const url = `${window.location.origin}/form.php?formId=${formId}`;
    const copyText = button.querySelector('.copy-text');

    navigator.clipboard.writeText(url).then(() => {
      const originalText = copyText.textContent;
      copyText.textContent = 'Copied!';
      button.classList.add('btn-success');

      setTimeout(() => {
        copyText.textContent = originalText;
        button.classList.remove('btn-success');
      }, 2000);
    }).catch(err => {
      const textArea = document.createElement('textarea');
      textArea.value = url;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);

      copyText.textContent = 'Copied!';
      button.classList.add('btn-success');
      setTimeout(() => {
        copyText.textContent = 'Share Form';
        button.classList.remove('btn-success');
      }, 2000);
    });
  }
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>