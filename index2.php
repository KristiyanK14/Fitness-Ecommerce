<?php
session_start();
include_once "database.php";
$sql="SELECT * FROM products LIMIT 8";
$all_products=$con->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Spotter</title>
    <link rel="stylesheet" href="style2.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Oswald:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php $currentPage = 'home'; 
include "navbar.php";?>
<style>
    .add button{
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

        .add button:hover{
            background-color: grey;
        }
</style>

<section id="hero">
    <h1>The Spotter You've Always Needed!</h1>
    <p>“       It is a shame for a man to grow old without seeing the beauty and strength of which his body is capable.”― Socrates</p>
    <a href="products.php"><button><h3>Begin Your Journey</h3> </button></a>
</section>
<section id="feature" class="section-p1">
    <div class="fe-box">
        <img src="images/delivery.jpg" width="120px" height="90px">
        <h4>Quick delivery</h4>
    </div>
    <div class="fe-box">
        <img src="images/advice.jpg" width="120px" height="90px">
        <h3>Information and Tips</h3>
    </div>
    <div class="fe-box">
        <img src="images/support.jpg" width="120px" height="90px">
        <h3>24/7 Support</h3>
    </div>
</section>
<h2 style="text-align: center;">Featured Products</h2>
<section id="product1" class="section-p1">
        <div class="pro-container">
            <?php
            while ($row = mysqli_fetch_assoc($all_products)) {
                $averageRating = getAverageRating($con, $row['productid']);
            ?>
                <div class="pro" style="cursor: pointer;" onclick="window.location.href='product_detail.php?id=<?php echo $row['productid']; ?>'">
                    <img src="<?php echo $row["imageurl"]; ?>" alt="">
                    <div class="category">
                        <span><?php echo $row["Category"]; ?></span>
                        <h3><?php echo $row["pname"]; ?></h3>
                        
                        <div class="rating-stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                $starClass = ($i <= $averageRating) ? 'fa fa-star' : 'fa fa-star-o';
                                echo '<i class="' . $starClass . '"></i>';
                            }
                            ?>
                        </div>
                        <h3 class="price">£<?php echo $row["price"]; ?></h3>
                        <?php
                            if ($row["stock"] > 0) {
                        ?>
                            <form class="add" action="add_to_basket.php" method="post">
                                <input type="hidden" name="productid" value="<?php echo $row['productid']; ?>">
                                <button type="submit" name="add_to_basket">Add to Basket</button>
                            </form>
                        <?php
                            } else {
                                echo "<p>Temporarily Out of Stock</p>";
                            }
                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </section>

    <section id="newsletter">
    <div class="news">
        <h3>Sign Up To Our Newsletter</h3>
        <p2>Once a part of the community, you will receive the latest news and deals</p2>
    </div>
    <div class="form">
        <form method="POST" action="NewsEmailer.php">
            <input type="email" name="email" placeholder="Your Email Address" required>
            <button type="submit" name="send">Sign Up</button>
        </form>
    </div>
</section>


<?php
function getAverageRating($con, $product_id)
{
    $query = "SELECT AVG(star) as average_rating FROM reviews WHERE productid = $product_id";
    $result = $con->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return round($row['average_rating']);
    }

    return 0; 
}
?>
</body>
</html>
