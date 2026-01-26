<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (isset($_SESSION["logged_in"])) {
  header('Location: dashboard.php');
  exit;
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
      <h2 class="text-xl font-semibold text-center">Welcome Back</h2>

      <form class="space-y-4" method="post" action="action.php?action=login">
        <input type="email" name="email" placeholder="Email" class="input input-bordered w-full" required />
        <input type="password" name="password" placeholder="Password" class="input input-bordered w-full" required />

        <button class="btn btn-primary w-full" type="submit">Login</button>
      </form>

      <p class="text-center text-sm opacity-70">
        Don't have an account?
        <a href="register.php" class="link link-primary">Register</a>
      </p>
    </div>
  </div>
</body>

</html>