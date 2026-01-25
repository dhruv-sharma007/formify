<!DOCTYPE html>
<html data-theme="dark">

<head>
  <title><?php echo isset($pageTitle) ? $pageTitle . ' | Formify' : 'Formify'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/daisyui@4/dist/full.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    body {
      background-color: #0f172a;
      /* Fallback */
      background-image: radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 1) 0, transparent 50%),
        radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 1) 0, transparent 50%),
        radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 1) 0, transparent 50%);
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
  </style>
</head>

<body
  class="<?php echo isset($bodyClass) ? $bodyClass : 'min-h-screen flex flex-col text-gray-100 font-sans antialiased'; ?>">

  <?php if (!isset($showNavbar) || $showNavbar): ?>
    <!-- Navbar -->
    <div class="navbar fixed top-0 z-50 transition-all duration-300 glass-effect border-b-0">
      <div class="flex-1 px-4">
        <a href="index.php"
          class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500 hover:opacity-80 transition-opacity">
          <i class="ri-checkbox-circle-fill mr-2 text-blue-500"></i>Formify
        </a>
      </div>

      <div class="flex gap-3">
        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
          <form action="action.php?action=logout" method="post">
            <button class="btn btn-sm btn-error btn-outline">Logout</button>
          </form>
        <?php else: ?>
          <a href="login.php" class="btn btn-sm btn-ghost hover:bg-white/10">Login</a>
          <a href="register.php"
            class="btn btn-sm btn-primary bg-gradient-to-r from-blue-600 to-purple-600 border-none hover:shadow-lg hover:shadow-blue-500/30 transition-all">Get
            Started</a>
        <?php endif; ?>
      </div>
    </div>
    <!-- Spacer for fixed navbar -->
    <div class="h-16"></div>
  <?php endif; ?>

  <!-- Main Content -->
  <div class="flex-grow">