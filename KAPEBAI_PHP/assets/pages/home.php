<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== BOX ICONS ===== -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../css/home.css">

    <title>Kape Bai Users</title>
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
                <a href="../pages/home.php" class="nav__link active">
                    <i class='bx bx-grid-alt nav__icon'></i>
                    <span class="nav__name">Dashboard</span>
                </a>

                <a href="../pages/manage_product.php" class="nav__link ">
                    <i class='bx bx-package nav__icon'></i>
                    <span class="nav__name">Manage Product</span>
                </a>

                <a href="../pages/menu.php" class="nav__link">
                    <i class='bx bx-food-menu nav__icon'></i>
                    <span class="nav__name">Menu</span>
                </a>

                <a href="../pages/orders.php" class="nav__link">
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
            <i class='bx bx-log-out nav__icon'></i>
            <span class="nav__name">Log Out</span>
        </a>
    </nav>
</div>

<!--===== MAIN JS =====-->
<script src="../js/main.js"></script>

<div class="total-container">
    <div class="total-bar">
        <?php
        // Connect to the database
        $connection = mysqli_connect("localhost", "root", "", "kapebai_db");
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
        // Query to calculate total orders
        $ordersQuery = "SELECT SUM(quantity) AS total_orders FROM orders";
        $ordersResult = mysqli_query($connection, $ordersQuery);
        $ordersData = mysqli_fetch_assoc($ordersResult);
        $totalOrders = $ordersData['total_orders'];
        ?>
        <div class="total-bar-fill" style="width: <?php echo ($totalOrders * 5); ?>px;"></div>
        <div class="total-label"><?php echo $totalOrders; ?> Total Orders</div>
    </div>
    <div class="total-bar">
        <?php
        // Query to calculate total sales
        $salesQuery = "SELECT SUM(total_price) AS total_sales FROM orders";
        $salesResult = mysqli_query($connection, $salesQuery);
        $salesData = mysqli_fetch_assoc($salesResult);
        $totalSales = $salesData['total_sales'];
        ?>
        <div class="total-bar-fill" style="width: <?php echo ($totalSales / 50); ?>px;"></div>
        <div class="total-label"><?php echo '₱' . $totalSales; ?> Total Sales</div>
    </div>
    <?php mysqli_close($connection); ?>
</div>

<div class="chart-container">
    <h2>Sales Analytics</h2>
    <label for="sales-date">Select Date: </label>
    <input type="date" id="sales-date" onchange="updateSalesChart()">
    <canvas id="sales-chart"></canvas>
</div>

<div class="slideshow-container">
    <div class="mySlides fade">
        <img src="../offers/1.png">
    </div>

    <div class="mySlides fade">
        <img src="../offers/2.png">
    </div>

    <div class="mySlides fade">
        <img src="../offers/3.png">
    </div>

    <!-- Next and previous buttons -->
    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>

<script>
    var slideIndex = 0;
    showSlides();

    function showSlides() {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 5000); // Change image every 2 seconds
    }

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function updateSalesChart() {
        const selectedDate = document.getElementById('sales-date').value;
        // Fetch data from the database based on the selected date using AJAX
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    updateChart(data);
                } else {
                    console.error('Failed to fetch data');
                }
            }
        };
        xhr.open('GET', 'fetch_sales_data.php?date=' + selectedDate, true);
        xhr.send();
    }

    function updateChart(data) {
        const labels = data.map(entry => entry.time);
        const values = data.map(entry => entry.total_price);
        salesChart.data.labels = labels;
        salesChart.data.datasets[0].data = values;
        salesChart.update();
    }
</script>

<script>
    const ctx = document.getElementById('sales-chart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Initial chart data
    const initialData = <?php
        // Connect to the database
        $connection = mysqli_connect("localhost", "root", "", "kapebai_db");
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query to fetch initial sales data (e.g., for today's date)
        $today = date('Y-m-d');
        $sql = "SELECT time, total_price FROM orders WHERE DATE(date) = '$today'";
        $result = mysqli_query($connection, $sql);

        // Fetch data as associative array
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        // Close connection
        mysqli_close($connection);

        // Output data as JSON
        echo json_encode($data);
    ?>;

    // Update the chart with initial data
    updateChart(initialData);
</script>

</body>
</html>
