<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login - Kantin Sejahtera</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .login-container {
      width: 350px;
      margin: 100px auto;
      padding: 30px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      border: none;
      background: #4CAF50;
      color: white;
      font-size: 1em;
      border-radius: 4px;
      cursor: pointer;
    }

    .error-msg {
      background: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 4px;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h2>🔑 Login Sistem</h2>
    <?php if (isset($_GET['error'])): ?>
      <p class="error-msg">Username atau password salah!</p>
    <?php endif; ?>
    <form action="proses_login.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>
  </div>
</body>

</html>