<?php
session_start();
include_once "database.php";
$product_id = isset($_GET['id']) ? $_GET['id'] : die('Product ID not specified.');
$product = getProductData($con, $product_id);
$pastReviews = getPastReviews($con, $product_id, 2);
$con->close();
function getProductData($con, $product_id)
{
    $query = "SELECT * FROM products WHERE productid = $product_id";
    $result = $con->query($query);

    if ($result->num_rows > 0) {
        $productData = $result->fetch_assoc();
        $categoryId = $productData['Category'];
        $relatedProductsQuery = "SELECT * FROM products WHERE Category = '$categoryId' AND productid != $product_id LIMIT 4";
        $relatedProductsResult = $con->query($relatedProductsQuery);
        if ($relatedProductsResult->num_rows > 0) {
            $productData['relatedProducts'] = $relatedProductsResult->fetch_all(MYSQLI_ASSOC);
        }
        return $productData;
    } else {
        die("Product not found");
    }
}

function getPastReviews($con, $product_id, $limit = 2)
{
    $reviews = array();

    $query = "SELECT * FROM reviews WHERE productid = $product_id ORDER BY created_at DESC LIMIT $limit";
    $result = $con->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
    }

    return $reviews;
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['productname']; ?> Details</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-KyZXEAg3QhqLMpG8r+Knujsl5+z0I5t9z/T9fexh7D5n2jo8ycFZgk2Ptc+NYIiWADgdz7NqRSWyIdQqQCKcRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Oswald:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <style>
        .add button{
            display: block;
            background-color: #333;
            color: white;
            border: none;
            padding: 25px 35px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .filtering button:hover,
        .add button:hover{
            background-color: grey;
        }
    </style>
</head>

<body>
    <?php $currentPage = 'product'; include "navbar.php";?>
    <div class="product-container">
        <img class="product-image" src="<?php echo $product["imageurl"]; ?>" alt="">
        <div class="product-detail">
            <h2><?php echo $product['pname']; ?></h2>
            <p><?php echo $product['description']; ?></p>
            <p class="price1">Price: $<?php echo $product['price']; ?></p>
            <?php if ($product['stock'] > 0) { ?>
                <form class="add" action="add_to_basket.php" method="post">
                <input type="hidden" name="productid" value="<?php echo $product_id; ?>">
                <button type="submit" name="add_to_basket">Add to Basket</button>
            </form>
            <?php    } else {
                echo "<p>Temporarily out of stock</p>"; 
            }?>
        </div>
    </div>

    <div class="past-reviews">
        <h2>Past Reviews</h2>
        <?php
        foreach ($pastReviews as $review) {
            ?>
            <div class="past-review">
                <div class="stars">
                    <?php
                    for ($i = 1; $i <= $review['star']; $i++) {
                        echo '<i class="fa fa-star"></i>';
                    }
                    ?>
                </div>
                <div class="user-info">
                    <p><?php echo "Annonymus user" ?></p>
                </div>
                <div class="review-text">
                    <p><?php echo $review['review']; ?></p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="review-container">
        <h2>Leave a Review</h2>
        <form action="process_review.php" method="post">
            <div>
                <label for="rating">Rating (Right To Left):</label>
                <div class="rating-stars">
                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        echo '<input type="radio" name="rating" value="' . $i . '" id="star' . $i . '">';
                        echo '<label for="star' . $i . '" title="' . $i . ' star">&#9733;</label>'; // &#9733; is the Unicode character for a filled star
                    }
                    ?>
                </div>
            </div>

            <div>
                <label for="review">Review:</label>
                <textarea name="review" id="review" rows="4" placeholder="Write your review here..."></textarea>
            </div>
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <div class="related-products">
        <h2>Similar Products</h2>
        <div class="related-container">
            <?php
            if (isset($product['relatedProducts']) && is_array($product['relatedProducts'])) {
                foreach ($product['relatedProducts'] as $relatedProduct) {
            ?>
                    <div class="rel">
                        <img src="<?php echo $relatedProduct['imageurl']; ?>" alt="">
                        <div class="des">
                            <h3><?php echo $relatedProduct['pname']; ?></h3>
                            <p class="price">Price: $<?php echo $relatedProduct['price']; ?></p>
                            <p class="price">Stock Left: <?php echo $relatedProduct['stock']; ?></p>
                            <a href="product_detail.php?id=<?php echo $relatedProduct['productid']; ?>">View Product</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No related products available</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
