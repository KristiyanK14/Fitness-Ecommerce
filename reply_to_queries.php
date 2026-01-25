<?php
session_start();
include_once "navbar.php";
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
include_once "database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Queries</title>
    <style>
        .query-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .query-box {
            width: 300px;
            border: 1px solid #ccc;
            margin: 10px;
            padding: 15px;
            border-radius: 5px;
        }
        .query-box textarea {
            width: 100%;
            margin-top: 10px;
        }
        .query-box button {
            width: 100%;
            padding: 8px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .query-box button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="query-container">

<?php
$query = "SELECT * FROM queries WHERE replied = 0";
$result = mysqli_query($con, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
?>
        <div class="query-box" id="query-box-<?php echo $row['queryid']; ?>">
            <p><strong>Query ID:</strong> <?php echo $row['queryid']; ?></p>
            <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
            <p><strong>Email:</strong> <span id="email-<?php echo $row['queryid']; ?>"><?php echo $row['email']; ?></span></p>
            <p><strong>Query:</strong> <?php echo $row['c_query']; ?></p>
            <p><strong>Query Reply:</strong></p>

            <div class="query-reply-box">
                <label for="query-reply">Query Reply:</label>
                <textarea id="query-reply-<?php echo $row['queryid']; ?>" name="query_reply"></textarea>
                <button type="button" class="send-reply-btn" onclick="sendReply(<?php echo $row['queryid']; ?>)">Send Reply</button>
            </div>
        </div>
<?php
    }
} else {
    echo "<p>No queries found.</p>";
}
mysqli_close($con);
?>

</div>

<script>
    function sendReply(queryId) {
        console.log("sendReply function called");
        var reply = document.querySelector('#query-reply-' + queryId).value;
        console.log("Query ID:", queryId); 
        console.log("Reply:", reply); 
        var emailField = document.querySelector('#email-' + queryId);
        if (!emailField) {
            console.error("Email address field not found");
            return;
        }
        var email = emailField.textContent.trim();
        console.log("Email:", email);
        if (!reply.trim() || !email) {
            console.error("Reply or email address not found");
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "send_reply.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                alert("Reply sent successfully.");
                location.reload();
            }
        };
        xhr.send("queryId=" + queryId + "&email=" + encodeURIComponent(email) + "&reply=" + encodeURIComponent(reply));
    }
</script>

</body>
</html>
