<?php
$pageTitle = "Fill Form";
// Fill form might not need logged in user, or maybe it does. Assuming public for now.
$showNavbar = false; // Usually filling a form doesn't show the main site navbar
require_once __DIR__ . '/layouts/header.php';
?>

<main class="max-w-xl mx-auto p-6 space-y-6">
  <h1 class="text-2xl font-semibold">Customer Feedback</h1>

  <div>
    <label class="label">How was your experience?</label>
    <input class="input input-bordered w-full">
  </div>

  <button class="btn btn-primary w-full">Submit</button>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>


