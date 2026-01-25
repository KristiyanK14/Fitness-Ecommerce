<?php
session_start();
include_once "database.php";

// average stars thing
function getAverageRating($con, $product_id)
{
    $query = "SELECT AVG(star) as average_rating FROM reviews WHERE productid = $product_id";
    $result = $con->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return round($row['average_rating']);
    }

    return 0; // 0 value if no review
}

// defuault sorting if user didnt bother chosing
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';

// SQL query to retrieve products
$sql = "SELECT * FROM products";


switch ($sort) {
    case 'low-to-high':
        $sql .= " ORDER BY price ASC";
        break;
    case 'high-to-low':
        $sql .= " ORDER BY price DESC";
        break;
    default:
        
        break;
}

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
if (!empty($categoryFilter)) {
    $sql .= " WHERE Category = '$categoryFilter'";
}

$all_products = $con->query($sql);


$per_page = 8; // num of products per page
$total_products = $all_products->num_rows;
$total_pages = ceil($total_products / $per_page);
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($current_page - 1) * $per_page;

$sql .= " LIMIT $start, $per_page";
$all_products = $con->query($sql);
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
    <style>
        /* Style for category sorting buttons */
        .filtering button {
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
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
        
        .filtering button:hover,
        .add button:hover{
            background-color: grey;
        }

        /* Style for filtering container */
        .filtering {
            margin-bottom: 20px;
        }

        /* Style for sorting select */
        .sorting select {
            padding: 8px 16px;
            border-radius: 4px;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .sorting select:hover {
            border-color: grey;
        }

        .sorting label {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <?php $currentPage = 'product';
    include "navbar.php"; ?>
    <section id="Page-header">
        <h1>Taste The Benefits!</h1>
    </section>
    <div class="sorting">
        <label for="sort">Sort By:</label>
        <select id="sort" name="sort" onchange="window.location.href='products.php?sort=' + this.value;">
            <option value="none" <?php if ($sort == 'none') echo 'selected'; ?>>-- Select --</option>
            <option value="low-to-high" <?php if ($sort == 'low-to-high') echo 'selected'; ?>>Price: Low to High</option>
            <option value="high-to-low" <?php if ($sort == 'high-to-low') echo 'selected'; ?>>Price: High to Low</option>
        </select>
    </div>
    <div class="filtering">
        <label for="filtering">Categories:</label>
        <button onclick="window.location.href='products.php?category=vitamins'">Vitamins</button>
        <button onclick="window.location.href='products.php?category=snacks'">Snacks</button>
        <button onclick="window.location.href='products.php?category=creatine'">Creatine</button>
        <button onclick="window.location.href='products.php?category=caffeinated'">Caffeinated</button>
        <button onclick="window.location.href='products.php?category=protein'">Protein</button>
    </div>
    <section id="product1" class="section-p1">
        <div class="pro-container">
            <?php
            while ($row = mysqli_fetch_assoc($all_products)) {
                // fetch average rating from the database
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
                                // If $i is less than or equal to the rounded average, display a filled star
                                $starClass = ($i <= $averageRating) ? 'fa fa-star' : 'fa fa-star-o';
                                echo '<i class="' . $starClass . '"></i>';
                            }
                            ?>
                        </div>
                        <h3 class="price">£<?php echo $row["price"]; ?></h3>
                        <?php
                            // Show "Add to Basket" button if stock is available
                            if ($row["stock"] > 0) {
                        ?>
                            <!-- index2.php -->
                            <form class="add" action="add_to_basket.php" method="post">
                                <input type="hidden" name="productid" value="<?php echo $row['productid']; ?>">
                                <button type="submit" name="add_to_basket">Add to Basket</button>
                            </form>
                        <?php
                            } else {
                                // Show "Temporarily Out of Stock" text if stock is 0
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
    <section id="pagination" class="section-p1">
        <?php if ($current_page > 1) : ?>
            <a href="products.php?page=<?php echo $current_page - 1; ?>"><i class="fa fa-long-arrow-left"></i></a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="products.php?page=<?php echo $i; ?>" <?php if ($i === $current_page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($current_page < $total_pages) : ?>
            <a href="products.php?page=<?php echo $current_page + 1; ?>"><i class="fa fa-long-arrow-right"></i></a>
        <?php endif; ?>
    </section>
    <section id="newsletter">
        <div class=" news">
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
