<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== BOX ICONS ===== -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../css/orders.css">

    <title>Kape Bai History</title>
</head>
<body id="body-pd">
<header class="header" id="header">
    <div class="header__toggle">
        <i class='bx bx-menu' id="header-toggle"></i>
    </div>

    <div class="header__img">
        <img src="../img/coffee.jpg" alt="">
    </div>
</header>

<div class="l-navbar" id="nav-bar">
    <nav class="nav">
        <div>
            <a href="#" class="nav__logo">
                <i class='bx bx-coffee-togo'></i>
                <span class="nav__logo-name">KAPE BAI</span>
            </a>

            <div class="nav__list">
                <a href="../pages/home.php" class="nav__link ">
                    <i class='bx bx-grid-alt nav__icon' ></i>
                    <span class="nav__name">Dashboard</span>
                </a>

                <a href="../pages/manage_product.php" class="nav__link ">
                    <i class='bx bx-package nav__icon' ></i>
                    <span class="nav__name">Manage Product</span>
                </a>

                <a href="../pages/menu.php" class="nav__link ">
                    <i class='bx bx-food-menu nav__icon'></i>
                    <span class="nav__name">Menu</span>
                </a>

                <a href="../pages/orders.php" class="nav__link active">
                    <i class='bx bxs-receipt nav__icon'></i>
                    <span class="nav__name">Orders</span>
                </a>
                <a href="../pages/sales_report.php" class="nav__link ">
                    <i class='bx bxs-report nav__icon'></i>
                    <span class="nav__name">Sales Reports</span>
                </a>
            </div>
        </div>

        <a href="../pages/index.php" class="nav__link">
            <i class='bx bx-log-out nav__icon' ></i>
            <span class="nav__name">Log Out</span>
        </a>
    </nav>
</div>

<!--===== MAIN JS =====-->
<script src="../js/main.js"></script>
<h1 class="customer_title">CUSTOMERS ORDERS</h1>
<?php
// Connect to database
$connection = mysqli_connect("localhost", "root", "", "kapebai_db");

// Check connection
if ($connection === false) {
    die("Error: Could not connect. " . mysqli_connect_error());
}

// Retrieve unique customer names from orders table
$query = "SELECT DISTINCT customer_name FROM orders WHERE payment_status <> 'Paid'";
$result = mysqli_query($connection, $query);

// Display orders grouped by customer name
while ($row = mysqli_fetch_assoc($result)) {
    $customerName = $row['customer_name'];
    ?>
    
    <h2><?php echo $customerName; ?></h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Time</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php
        // Retrieve orders for the current customer that are not paid
        $query = "SELECT * FROM orders WHERE customer_name = '$customerName' AND payment_status <> 'Paid'";
        $ordersResult = mysqli_query($connection, $query);

        // Display order details
        $totalAmount = 0;
        while ($order = mysqli_fetch_assoc($ordersResult)) {
            $totalAmount += $order['total_price'];
            ?>
            <tr>
                <td><?php echo $order['Pname']; ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td><?php echo $order['date']; ?></td>
                <td><?php echo $order['time']; ?></td>
                <td><?php echo $order['total_price']; ?></td>
                <td>
                    <form class="delete-form" method="post" action="delete_order.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="customer_name" value="<?php echo $customerName; ?>">
                        <input type="hidden" name="Pname" value="<?php echo $order['Pname']; ?>">
                        <input type="hidden" name="date" value="<?php echo $order['date']; ?>">
                        <input type="hidden" name="time" value="<?php echo $order['time']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="4"><strong>Total:</strong></td>
            <td><strong><?php echo $totalAmount; ?></strong></td>
            <td colspan="2"></td>
        </tr>
    </table>
    <!-- Form for Tendered, Change, and Type -->
    <form method="post" action="process_payment.php" onsubmit="return confirm('Do you really want to make a receipt?');">
        <input type="hidden" name="customerName" value="<?php echo $customerName; ?>">
        <label for="tendered_<?php echo $customerName; ?>">Tendered:</label>
        <input type="number" id="tendered_<?php echo $customerName; ?>" name="tendered" step="0.01" required><br>
        <label for="type_<?php echo $customerName; ?>">Type:</label>
        <select id="type_<?php echo $customerName; ?>" name="type" required>
            <option value="Cash">Cash</option>
            <option value="Gcash">Gcash</option>
            <option value="Paypal">Paypal</option>
        </select><br>
        <label for="change_<?php echo $customerName; ?>">Change:</label>
        <span id="change_<?php echo $customerName; ?>"></span><br> <!-- Empty span to display change -->
        <button class="calculate-change-btn" type="button" onclick="calculateChange('<?php echo $customerName; ?>', <?php echo $totalAmount; ?>)">Calculate Change</button>
        <button type="submit">Make Receipt</button>
    </form>

    <?php
}

// Close connection
mysqli_close($connection);

?>
<script>
    function calculateChange(customerName, totalAmount) {
        // Retrieve tendered amount
        var tendered = parseFloat(document.getElementById('tendered_' + customerName).value);

        // Validate tendered amount
        if (isNaN(tendered) || tendered < totalAmount) {
            alert('Tendered amount should be a valid number and greater than or equal to the total amount.');
            return;
        }

        // Calculate change
        var change = tendered - totalAmount;

        // Display change
        document.getElementById('change_' + customerName).textContent = '₱' + change.toFixed(2);
    }
</script>


    
</body>
</html>
