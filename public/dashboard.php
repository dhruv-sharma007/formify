<?php
$pageTitle = "Dashboard";
// $isLoggedIn = true;
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/layouts/header.php';
?>

<main class="max-w-6xl mx-auto p-6">
  <div class="flex justify-between mb-6">
    <h1 class="text-xl font-semibold">Your Forms</h1>
    <a href="form-create.php" class="btn btn-primary">+ New Form</a>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    <div class="card bg-base-100 shadow">
      <div class="card-body">
        <h2 class="font-semibold">Customer Feedback</h2>
        <p class="text-sm opacity-70">12 responses</p>

        <div class="flex gap-2 mt-4">
          <a class="btn btn-sm">Edit</a>
          <a class="btn btn-sm btn-outline">Analytics</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>