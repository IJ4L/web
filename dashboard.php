<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/dashboard_style.css">
</head>

<body>
    <p>Welcome to the Dashboard Page</p>

    <?php
    $host = "127.0.0.1";
    $username = "root";
    $password = "";
    $database = "login";

    // Create a database connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch news data from the database
    $sql = "SELECT * FROM news";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        echo '<table>';
        echo '<tr><th>ID</th><th>Judul Berita</th><th>Isi Berita</th><th>Tags</th><th>Gambar</th><th>Aksi</th></tr>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['content'] . '</td>';
            echo '<td>' . $row['tags'] . '</td>';
            echo '<td><img src="' . $row['images'] . '" alt="Gambar Berita"></td>';
            echo '<td class="action-buttons">';
            echo '<button class="delete" onclick="deleteNews(' . $row['id'] . ')">Hapus</button>';
            echo '<button class="update" onclick="updateNews(' . $row['id'] . ')">Update</button>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '0 results';
    }

    // Close the database connection
    $conn->close();
    ?>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function deleteNews(newsId) {
            // Use AJAX to send the news ID to the delete_news.php script
            $.post('delete_news.php', { news_id: newsId }, function (response) {
                console.log(response);
                // You can refresh the page or update the UI as needed
            });
        }

        function updateNews(newsId) {
            window.location.href = 'add-news.php?news_id=' + newsId;
        }
    </script>
</body>

</html>