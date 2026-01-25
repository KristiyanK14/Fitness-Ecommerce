<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Spotter Account Managment</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Oswald:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php 
    $currentPage = 'login'; 
    include "navbar.php";
    ?>

<section id="login">
<h1>Login</h1>
<form action="process-login.php" method="post" validate>
    <div>
        <label for="name">name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="email">email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
        <div>
            <button type="submit" name="update">Login</button>
            <label>
            <input type="checkbox" checked="checked" name="remember"> Remember me
            </label>
        </div>
      <div class="container" style="background-color:#f1f1f1">
        <button type="button" class="cancelbtn">Cancel</button>
        <span class="password">Forgot <a href="forgot_password.php">password?</a></span>
        
        <div class="register-link">
            <p>Dont have an account?: <a href="signup.php">
                signup</a></p>
        </div>
      </div>
</section>
</form>