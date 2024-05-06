<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_name'], $_POST['Pname'], $_POST['date'], $_POST['time'])) {
    // Retrieve form data
    $customerName = $_POST['customer_name'];
    $productName = $_POST['Pname'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Connect to the database
    $connection = mysqli_connect("localhost", "root", "", "kapebai_db");

    // Check connection
    if ($connection === false) {
        die("Error: Could not connect. " . mysqli_connect_error());
    }

    // Prepare and execute the delete query
    $deleteQuery = "DELETE FROM orders WHERE customer_name = '$customerName' AND Pname = '$productName' AND date = '$date' AND time = '$time'";
    if (mysqli_query($connection, $deleteQuery)) {
        // If deletion is successful, redirect to the orders page
        header("Location: orders.php");
        exit();
    } else {
        // If deletion fails, display an error message
        echo "Error deleting order: " . mysqli_error($connection);
    }

    // Close connection
    mysqli_close($connection);
} else {
    // If the form is not submitted correctly, redirect to the orders page
    header("Location: orders.php");
    exit();
}
?>
