<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Oswald:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <section id="header">
        <a href="index2.php"> <img src="images/logo.jpg" width="125px" alt=""></a>
        <div>
            <?php
            
            if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
            ?>
            <ul id="navbar-admin" style="display">
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                <li><a href="manage_tables.php">Database</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <?php
            } else if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'staff') {
            ?>
            <ul id="navbar-admin" style="display">
                <li><a href="admin_dashboard.php">Staff Dashboard</a></li>
                <li><a href="staff_database.php">Database</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <?php
            } else {
            ?>
            <ul id="navbar-logged-out" style="display: <?php echo isset($_SESSION['user_id']) ? 'none' : 'flex'; ?>;">
                <li><a href="index2.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fa fa-user"></i></a>
                    <div class="dropdown-content">
                        <a href="login.php">Login</a>
                        <a href="signup.php">Register</a>
                    </div>
                </li>
            </ul>
            <ul id="navbar-logged-in" style="display: <?php echo isset($_SESSION['user_id']) ? 'flex' : 'none'; ?>;">
                <li><a href="index2.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php"><i class="fa fa-shopping-basket"></i></a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fa fa-user"></i></a>
                    <div class="dropdown-content">
                        <a href="account.php">Account</a>
                      
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <?php
            }
            ?>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            var navbarLoggedOut = document.getElementById("navbar-logged-out");
            var navbarLoggedIn = document.getElementById("navbar-logged-in");
            var navbarAdmin = document.getElementById("navbar-admin");
            var navbarStaff = document.getElementById("navbar-staff");

            navbarLoggedOut.style.display = isLoggedIn ? "none" : "flex";
            navbarLoggedIn.style.display = isLoggedIn ? "flex" : "none";
            navbarAdmin.style.display = isLoggedIn && '<?php echo $_SESSION['user_role']; ?>' === 'admin' ? 'block' : 'none';
            navbarStaff.style.display = isLoggedIn && '<?php echo $_SESSION['user_role']; ?>' === 'staff' ? 'block' : 'none';
        });
    </script>
</body>
</html>
<style>
    .dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: lightgrey;
  min-width: 120px;
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: red;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #9d9d9d;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: darkgrey;
}

</style>
