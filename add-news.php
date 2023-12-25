<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Berita</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="css/add-news-style.css">
</head>

<body>

  <div class="container">

    <?php
    // Tampilkan pesan sukses jika ada
    if (isset($_GET['success'])) {
      echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
    }

    // Initialize variables for existing data
    $existingTitle = '';
    $existingContent = '';
    $existingTags = array();

    // If news_id is provided, fetch the existing news details
    $newsId = isset($_GET['news_id']) ? $_GET['news_id'] : null;
    if ($newsId) {
      // Fetch the existing news details based on $newsId
      // Adjust the logic to populate the form fields for editing
      // ...
      // Sample logic (please customize based on your actual database structure)
      $host = "127.0.0.1";
      $username = "root";
      $password = "";
      $database = "login";

      $conn = new mysqli($host, $username, $password, $database);

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT * FROM news WHERE id = ?";
      $statement = $conn->prepare($sql);
      $statement->bind_param('i', $newsId);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existingTitle = $row['title'];
        $existingContent = $row['content'];
        $existingTags = explode(', ', $row['tags']);
      }

      $statement->close();
      $conn->close();
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
      <h2><i class="fas fa-plus-circle"></i>
        <?php echo $newsId ? 'Update' : 'Tambah'; ?> Berita
      </h2>
      <input type="hidden" name="news_id" value="<?php echo $newsId; ?>">

      <label for="title">Judul Berita:</label>
      <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($existingTitle); ?>">

      <label for="content">Isi Berita:</label>
      <textarea id="content" name="content" required><?php echo htmlspecialchars($existingContent); ?></textarea>

      <label for="tags">Tag:</label>
      <select id="tags" name="tags[]" multiple>
        <option value="politik" <?php echo in_array('politik', $existingTags) ? 'selected' : ''; ?>>Politik</option>
        <option value="teknologi" <?php echo in_array('teknologi', $existingTags) ? 'selected' : ''; ?>>Teknologi
        </option>
        <option value="hiburan" <?php echo in_array('hiburan', $existingTags) ? 'selected' : ''; ?>>Hiburan</option>
      </select>

      <label for="image">Gambar:</label>
      <input type="file" id="image" name="image" accept="image/*">

      <button type="submit" name="submit"><i class="fas fa-check"></i>
        <?php echo $newsId ? 'Update' : 'Tambah'; ?> Berita
      </button>
    </form>
  </div>

  <script src="https://kit.fontawesome.com/your-fontawesome-kit-id.js"></script>
  <?php
  // process_news.php
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Pastikan formulir telah disubmit
  
    // Handle data dari formulir
    $newsId = isset($_POST['news_id']) ? $_POST['news_id'] : null;
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $tags = isset($_POST['tags']) ? implode(', ', $_POST['tags']) : ''; // Ubah array tags menjadi string
    $imagePath = '';

    // Handle gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
      $imagePath = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
      $imagePath = '../news-page/uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
      // Pindahkan gambar ke folder uploads
      if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        echo 'Error uploading image.';
        exit();
      }
    }

    // Simpan data ke database menggunakan MySQLi
    $host = "127.0.0.1";
    $username = "root";
    $password = "";
    $database = "login";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    try {
      // Check if news ID is provided for updating
      if ($newsId) {
        // Check if the image is being updated
        if ($imagePath) {
          // Delete the previous image file if it exists
          $sqlDeleteImage = "SELECT images FROM news WHERE id = ?";
          $stmtDeleteImage = $conn->prepare($sqlDeleteImage);
          $stmtDeleteImage->bind_param('i', $newsId);
          $stmtDeleteImage->execute();
          $stmtDeleteImage->bind_result($existingImagePath);
          $stmtDeleteImage->fetch();
          $stmtDeleteImage->close();

          if ($existingImagePath) {
            unlink($existingImagePath);
          }

          // Update the news with the new image path
          $sqlUpdate = "UPDATE news SET title = ?, content = ?, tags = ?, images = ? WHERE id = ?";
          $stmtUpdate = $conn->prepare($sqlUpdate);
          $stmtUpdate->bind_param('ssssi', $title, $content, $tags, $imagePath, $newsId);
          $stmtUpdate->execute();
          $stmtUpdate->close();
        } else {
          // Update the news without changing the image
          $sqlUpdate = "UPDATE news SET title = ?, content = ?, tags = ? WHERE id = ?";
          $stmtUpdate = $conn->prepare($sqlUpdate);
          $stmtUpdate->bind_param('sssi', $title, $content, $tags, $newsId);
          $stmtUpdate->execute();
          $stmtUpdate->close();
        }
      } else {
        // Insert new news
        $sqlInsert = "INSERT INTO news (title, content, tags, images) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param('ssss', $title, $content, $tags, $imagePath);
        $stmtInsert->execute();
        $stmtInsert->close();
      }

      // Set success message
      $successMessage = 'Berita ' . ($newsId ? 'diupdate' : 'ditambahkan') . '!';
      header('Location: index.php?success=' . urlencode($successMessage));
      exit();
    } catch (Exception $e) {
      // Handle kesalahan koneksi, query, atau eksekusi statement
      echo 'Error: ' . $e->getMessage();
      exit();
    } finally {
      // Tutup koneksi
      $conn->close();
    }
  }
  ?>
</body>

</html>