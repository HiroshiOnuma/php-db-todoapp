<?php
session_start();
require('library.php');
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $user_id = $_SESSION['id'];
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}
$db = dbconnect();

$title = '写真一覧 | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'pictures-page';
$heading_title = '写真一覧';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>
        <div class="container pictures-container" id="pictures-container">

            <div class="container pictures-wrapper">
                <!-- 画像を取得 -->
                <?php $stmt = $db->prepare('select id, file_name,file_path from file_data where user_id=?');
                if (!$stmt) {
                    die($db->error);
                }
                $stmt->bind_param('i', $user_id);
                $success = $stmt->execute();
                if (!$success) {
                    die($db->error);
                }
                $stmt->bind_result($file_id, $file_name, $file_path);
                ?>
                <?php while ($stmt->fetch()) : ?>
                    <div class="picture-item">
                        <img src="<?php echo $file_path; ?>" alt="">
                        <a class="delete-btn" href="delete.php?pictures_image_id=<?php echo h($file_id); ?>">
                            <button class="delete-btn">
                                <span class="material-symbols-outlined">
                                    delete
                                </span>
                            </button>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<div class="nav">
    <div class="inner nav-area">
        <button class="hamburger-btn">
            <img src="img/hamburger.png" alt="">
        </button>
        <a href="#pictures-container">
            <button class="picture-btn">
                <span class="material-symbols-outlined">
                    image
                </span>
            </button>
        </a>

    </div>
</div>
<script src="js/script.js"></script>
</body>

</html>