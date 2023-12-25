<?php
// Check if news ID is provided
if (isset($_POST['news_id'])) {
    $newsId = $_POST['news_id'];

    // Connect to the database (use your own database credentials)
    $conn = new mysqli("127.0.0.1", "root", "", "login");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Perform the deletion
    $sql = "DELETE FROM news WHERE id = $newsId";
    if ($conn->query($sql) === TRUE) {
        echo "News deleted successfully";
    } else {
        echo "Error deleting news: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "News ID not provided";
}
?>