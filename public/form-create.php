<?php
$pageTitle = "Create Form";
$isLoggedIn = true;
require_once __DIR__ . '/layouts/header.php';
?>

<main class="max-w-4xl mx-auto p-6 space-y-6">

  <!-- Form Start -->
  <form action="submit.php" method="POST">
      
      <div class="card bg-base-100 shadow mb-6 border-t-4 border-primary">
        <div class="card-body">
          <input type="text" name="title" class="input text-3xl font-semibold w-full focus:outline-none" placeholder="Untitled Form" required>
          <textarea name="description" class="textarea mt-2 w-full focus:outline-none resize-none" placeholder="Form description"></textarea>
        </div>
      </div>

      <!-- Questions Container -->
      <div id="questions-container" class="space-y-4">
          <!-- Dynamic questions will be appended here -->
      </div>

      <div class="flex flex-col gap-4 mt-6">
        <button type="button" id="add-question-btn" class="btn btn-outline border-dashed w-full">+ Add Question</button>
        
        <div class="flex justify-end sticky bottom-0 bg-base-100/80 backdrop-blur p-4 rounded-box shadow-lg border border-base-200 gap-2">
            <button type="submit" name="save_form" class="btn btn-primary">Save Form</button>
        </div>
      </div>

  </form>

</main>

<script src="js/form-builder.js"></script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>