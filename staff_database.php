<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables - Admin Dashboard</title>
    <style>
        body {
            background-color: #f5f5f5; 
            color: #333; 
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333; 
        }
        button {
            padding: 10px 20px;
            background-color: grey; 
            color: #fff; 
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: lightgrey; 
        }
        select {
            padding: 8px;
            border: 1px solid #ccc; 
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd; 
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2; 
        }
        .delete-btn {
            color: #d9534f; 
            cursor: pointer;
            margin-right: 5px;
        }
        .delete-btn:hover {
            text-decoration: underline;
        }
        .messages {
            margin-bottom: 20px;
        }
        .error {
            color: #d9534f; 
            margin-bottom: 10px;
        }
        /* New styles for search container */
        .search-container {
            margin-bottom: 10px;
        }
        .search-container input[type=text] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
        }
        .search-container button.clear {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
<?php
session_start();
include "navbar.php";
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
include_once "database.php";
function getTablesList($con) {
    $result = mysqli_query($con, "SHOW TABLES");
    $tables = array();
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    return $tables;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_table'])) {
    if(isset($_POST['table'])) {
        $selected_table = $_POST['table'];
        $query = "SELECT * FROM $selected_table";
        if(isset($_POST['sort'])) {
            if($_POST['sort'] === 'new-old') {
                $primary_key_query = "SHOW KEYS FROM $selected_table WHERE Key_name = 'PRIMARY'";
                $primary_key_result = mysqli_query($con, $primary_key_query);
                $primary_key_row = mysqli_fetch_assoc($primary_key_result);
                $primary_key_column = $primary_key_row['Column_name'];
                $query .= " ORDER BY $primary_key_column DESC";
            } elseif($_POST['sort'] === 'old-new') {
                $primary_key_query = "SHOW KEYS FROM $selected_table WHERE Key_name = 'PRIMARY'";
                $primary_key_result = mysqli_query($con, $primary_key_query);
                $primary_key_row = mysqli_fetch_assoc($primary_key_result);
                $primary_key_column = $primary_key_row['Column_name'];
                $query .= " ORDER BY $primary_key_column ASC";
            }
        }
        $result = mysqli_query($con, $query);
        if ($result) {
            echo "<h2>Selected table: $selected_table</h2>";
            echo "<table>";
            $row = mysqli_fetch_assoc($result);
            echo "<tr>";
            foreach ($row as $column => $value) {
                echo "<th>$column</th>";
            }
            echo "<th>Edit/Delete</th>";
            echo "</tr>";
            mysqli_data_seek($result, 0);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "<td><a href='edit_table.php?table=$selected_table&data=" . urlencode(json_encode($row)) . "'>Edit</a> <a href='#' data-table='$selected_table' data-row='" . urlencode(json_encode($row)) . "' class='delete-btn'>Delete</a></td>";
                    echo "</tr>";
                }
                $numColumns = mysqli_num_fields($result);
                echo "<tr><td colspan='" . ($numColumns + 1) . "'><a href='add_row.php?table=$selected_table'>Add Row</a></td></tr>";
            } else {
                echo "<tr><td colspan='2'>No rows found in $selected_table.</td></tr>";
            }
        }
    }
}
?>

<div class="container">
    <div class="messages">
    </div>
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for primary key...">
        <button type="button" onclick="clearSearch()" class="clear">Clear</button>
    </div>
    <form method="post" action="">
        <button type="submit" name="table_selection">List Tables</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['table_selection'])) {
        $tables = getTablesList($con);
        $allowed_tables = ['reviews', 'products'];
        $display_tables = array_intersect($tables, $allowed_tables);
        if (!empty($display_tables)) {
            echo '<form method="post" action="">';
            echo '<label for="table">Select a table:</label>';
            echo '<select name="table" id="table">';
            foreach ($display_tables as $table) {
                echo "<option value=\"$table\">$table</option>";
            }
            echo '</select>';
            echo '<button type="submit" name="show_table">Show Table</button>';
            echo '</form>';
        } else {
            echo "No tables found in the database.";
        }
    }
    ?>
</div>

<script>
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementsByTagName("table")[0]; 
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    function clearSearch() {
        var input = document.getElementById("searchInput");
        input.value = "";
        searchTable();
    }
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this row?')) {
                var row = JSON.parse(decodeURIComponent(event.target.getAttribute('data-row')));
                var table = event.target.getAttribute('data-table');
                var primaryKeyColumnName = Object.keys(row)[0];

                var formData = new FormData();
                formData.append('table', table);
                formData.append('primaryKeyColumnName', primaryKeyColumnName);
                formData.append('primaryKeyValue', row[primaryKeyColumnName]);

                fetch('delete_row.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }
    });
</script>

</body>
</html>
