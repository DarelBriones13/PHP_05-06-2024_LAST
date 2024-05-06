    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $customerName = $_POST['customerName'];
        $tendered = $_POST['tendered'];
        $type = $_POST['type'];

        // Connect to database
        $connection = mysqli_connect("localhost", "root", "", "kapebai_db");

        // Calculate total amount for unpaid orders
        $query = "SELECT SUM(total_price) AS totalAmount FROM orders WHERE customer_name = '$customerName' AND payment_status != 'Paid'";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($result);
        $totalAmount = $row['totalAmount'];

        // Calculate change only if there are unpaid orders
        if ($totalAmount !== null) {
            $change = $tendered - $totalAmount;

            // Update payment status in the database for unpaid orders
            $updateQuery = "UPDATE orders SET payment_status = 'Paid' WHERE customer_name = '$customerName' AND payment_status != 'Paid'";
            mysqli_query($connection, $updateQuery);

            // Create receipt content
            $dateTime = date("Y-m-d H:i:s"); // Current date and time
            $receiptContent = "
                Receipt\n\n
                Date and Time: $dateTime\n
                +------------------------+---------------------+
                |   Customer Name        | $customerName       
                +------------------------+---------------------+
                |   Total Amount         | ₱" . number_format($totalAmount, 2) . "        
                +------------------------+---------------------+
                |   Tendered             | ₱" . number_format($tendered, 2) . "        
                +------------------------+---------------------+
                |   Change               | ₱" . number_format($change, 2) . "        
                +------------------------+---------------------+
                |   Payment Type         | $type           
                +------------------------+---------------------+
            ";

            // Define the filename for the receipt
            $fileName = 'receipt_' . time() . '.txt';
            $filePath = '../receipts/' . $fileName; // Path to the receipts folder

            // Save receipt to a text file
            if (!file_exists('../receipts')) {
                mkdir('../receipts', 0777, true);
            }
            if (file_put_contents($filePath, $receiptContent)) {
                // Prompt the user to download the receipt
                header("Content-type: text/plain");
                header("Content-Disposition: attachment; filename=$fileName");
                echo $receiptContent;
                exit();
            } else {
                // If the file couldn't be saved, display an error message
                echo "Failed to save the receipt.";
            }
        }

        // Close connection
        mysqli_close($connection);

        // Redirect to the orders page
        header("Location: orders.php");
        exit();
    } else {
        // If the form is not submitted, redirect to the orders page
        header("Location: orders.php");
        exit();
    }
    ?>
