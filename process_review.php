<?php
session_start();
include_once "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = isset($_SESSION['customerID']) ? $_SESSION['customerID'] : die('User not logged in.');
    $productID = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $query = "INSERT INTO reviews (customerID, productID, star, review) VALUES ('$customerID', '$productID', '$rating', '$review')";
    $result = $con->query($query);

    if ($result) {
        echo "Review submitted successfully!";
    } else {
        echo "Error submitting review: " . $con->error;
    }

    $con->close();
}
?>
