<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Spotter Account Management</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Oswald:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include "navbar.php";
 if (isset($_GET['errors']) && !empty($_GET['errors'])) {
     $errors = explode("\n", urldecode($_GET['errors']));
     echo '<script>alert("' . implode('\\n', $errors) . '");</script>';
 }
 ?>
<section id="signup">
    <h1>Signup</h1>
    <form action="process-signup.php" method="post" validate>
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
        <div style="color: red;">Password needs to include a number and a letter and should be longer than 8 characters</div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="password_confirmation">Repeat password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit">Sign Up</button>
        <div class="register-link">
            <p>Already have an account?<a href="login.php">Login</a></p>
        </div>
    </form>
<script>
    document.getElementById('signupButton').addEventListener('click', function(event) {
        var name = document.getElementById('name').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;
        var passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (name === '' || email === '' || password === '' || passwordConfirmation === '') {
            alert('Please fill in all fields');
            event.preventDefault(); 
        }
    });
</script>
</section>
</body>
</html>
