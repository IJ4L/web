<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

  <aside>
    <h1>Admin</h1>
    <nav>
      <a href="index.php?page=dashboard"> <i class="icon" data-feather="home"></i> </a>
      <a href="index.php?page=add_news"> <i class="icon" data-feather="plus"></i> </a>
    </nav>
  </aside>

  <main id="mainContent">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

    switch ($page) {
      case 'dashboard':
        include 'dashboard.php';
        break;
      case 'add_news':
        include 'add-news.php';
        break;
      default:
        include 'dashboard.php';
        break;
    }
    ?>
  </main>
  <script>
    feather.replace();
  </script>
</body>

</html>