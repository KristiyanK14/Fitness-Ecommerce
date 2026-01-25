<?php
session_start();
include_once "database.php";
include "navbar.php";
$errors = [];
$successMessage = "";
function calculateBMI($weight, $height)
{
    return $weight / (($height / 100) * ($height / 100));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["fitness_tips"])) {
    }

    if (isset($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($_POST["c_query"])) {
        $errors[] = "Query cannot be empty";
    }
    if (empty($errors)) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $c_query = $_POST["c_query"];
        $sql = "INSERT INTO queries (name, email, c_query)
            VALUES ('$name', '$email', '$c_query')";
        if (mysqli_query($con, $sql)) {
            $successMessage = "Query submitted successfully";
        } else {
            $errors[] = "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
    if (!empty($_POST["bmi_weight"]) && !empty($_POST["bmi_height"]) && !empty($_POST["bmi_age"])) {
        $bmi_weight = $_POST["bmi_weight"];
        $bmi_height = $_POST["bmi_height"];
        $bmi_age = $_POST["bmi_age"];
        $bmi = calculateBMI($bmi_weight, $bmi_height);
        $bmi_message = '';
        if ($bmi < 18.5) {
            $bmi_message = "You are underweight.";
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $bmi_message = "You are within a healthy weight range.";
        } elseif ($bmi >= 25 && $bmi < 30) {
            $bmi_message = "You are overweight.";
        } else {
            $bmi_message = "You are obese.";
        }
        $successMessage = "Your BMI is: " . number_format($bmi, 2) . ". " . $bmi_message;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            color: #333;
        }

        button {
            margin-top: 70px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
            padding: 20px;
            box-sizing: border-box;
        }

        .box {
            width: calc(50% - 20px);
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-sizing: border-box;
        }

        #queryForm {
            margin: 20px auto;
            width: 50%;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            display: none;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .box {
                width: 100%;
                margin-bottom: 20px;
            }

            #queryForm {
                width: 80%;
            }
        }
    </style>
    <script>
        function toggleFormVisibility() {
            var form = document.getElementById("queryForm");
            form.style.display = form.style.display === "none" ? "block" : "none";
        }
    </script>
</head>
<body>

<?php if (empty($successMessage)): ?>
    <button onclick="toggleFormVisibility()">Make a Query</button>
    <form id="queryForm" method="POST" action="emailer.php" style="display: none;">
        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endforeach; ?>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="c_query">Query:</label>
        <textarea id="c_query" name="c_query" rows="4" required></textarea>

        <button type="submit" name="send">Submit Query</button>
    </form>
    <script>
    <?php if (!empty($_GET['successMessage'])): ?>
        alert('<?php echo $_GET['successMessage']; ?>');
    <?php endif; ?>
</script>
    <div class="container">
        <div class="box">
            <h2>Fitness Tips</h2>
            <p>Embarking on your fitness journey? Start with a well-rounded routine that includes a mix of cardiovascular exercises, strength training, and flexibility work. Don't forget the importance of proper form to prevent injuries, stay hydrated, and listen to your body's signals. Consistency is key. Set realistic goals, stay committed, and celebrate your progress along the way. Remember, it's not just about the destination, but the journey to a healthier and happier you!</p>
        </div>
        <div class="box">
            <h2>BMI Scale</h2>
            <p>Discover your body's composition with our BMI scale, a simple yet effective tool that evaluates your weight in relation to your height. A BMI below 18.5 suggests underweight, 18.5 to 24.9 is normal, 25 to 29.9 indicates overweight, and 30 or above signals obesity. This quick assessment provides valuable insights into your overall health and potential associated risks. Learn more about your body and take a step towards a healthier you!</p>
            <form method="post" action="">
                <label for="bmi_weight">Weight (kg):</label>
                <input type="number" id="bmi_weight" name="bmi_weight" required>
                <label for="bmi_height">Height (cm):</label>
                <input type="number" id="b
                mi_height" name="bmi_height" required>
              <label for="bmi_age">Age:</label>
              <input type="number" id="bmi_age" name="bmi_age" required>
              <button type="submit">Calculate BMI</button>
            </form>
        </div>
    </div>
    <?php else: ?>
        <div class="container">
        <div class="box">
        <p><?php echo $successMessage; ?></p>
    </div>
</div>
<button onclick="window.location.href='contact.php'">Return to Contact Page</button>
<?php endif; ?>
<section id="newsletter">
    <div class="news">
        <h3>Sign Up To Our Newsletter</h3>
        <p2>Once a part of the community, you will receive the latest news and deals</p2>
    </div>
    <div class="form">
        <form method="POST" action="NewsEmailer.php">
            <input type="text" name="email" placeholder="Your Email Address">
            <button type="submit" name="send">Sign Up</button>
        </form>
    </div>
</section>
</body>
</html>
