<?php
session_start();
if (isset($_SESSION["logged_in"])) {
  header('Location: dashboard.php');
}
?>

<!DOCTYPE html>
<html data-theme="dark">

<head>
  <title>Auth</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4/dist/full.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-base-200">
  <div class="card w-full max-w-md bg-base-100 shadow-xl">
    <div class="card-body">
      <h2 class="text-xl font-semibold text-center">Register</h2>

      <form class="space-y-4" method="post" action="action.php?action=register">
        <input type="text" name="name" required placeholder="Name" class="input input-bordered w-full" />
        <input type="email" name="email" required placeholder="Email" class="input input-bordered w-full" />
        <input type="password" name="password" required placeholder="Password" class="input input-bordered w-full" />
        <button class="btn btn-primary w-full">Register</button>
      </form>

      <p class="text-center text-sm opacity-70">
        Already have an account?
        <a href="login.php" class="link link-primary">Login</a>
      </p>
    </div>
  </div>
</body>

</html>