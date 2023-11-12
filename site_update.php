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
$site_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$site_id) {
    header("Location: index.php");
    exit();
}

$db = dbconnect();

$stmt = $db->prepare('select site_name, site_address, site_remarks from sites where id=? and user_id=?');
if (!$stmt) {
    die($db->error);
}
$stmt->bind_param('ii', $site_id, $user_id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($site_name, $site_address, $site_remarks);
$result = $stmt->fetch();
if (!$result) {
    die('業務現場の指定が正しくありません');
}
$stmt->close();

$title = '業務現場編集 | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'site-update-page';
$heading_title = '業務現場一覧';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>
        <form action="site_update_do.php" method="post" class="site-form" id="site-form">
            <dl>
                <dt class="form-heading site-form-heading">業務現場を編集</dt>
                <dd>
                    <input type="hidden" name="id" value="<?php echo $site_id; ?>">
                    <label for="site-name">現場の名称</label>
                    <input type="text" id="site-name" name="site_name" value="<?php echo h($site_name); ?>">
                    <label for="site-address">住所</label>
                    <input type="text" id="site-address" name="site_address" value="<?php echo h($site_address); ?>">
                    <label for="site_remarks">備考</label>
                    <textarea name="site_remarks" cols="30" rows="5"><?php echo h($site_remarks); ?></textarea>
                </dd>
            </dl>
            <input type="submit" value="業務現場を編集" class="form-btn">
        </form>

    </div>
</div>
<div class="nav">
    <div class="inner nav-area">
        <button class="hamburger-btn">
            <img src="img/hamburger.png" alt="">
        </button>
        <a href="#site-form">
            <button class="add-btn">
                <img src="img/plus.png" alt="">
            </button>
        </a>
    </div>
</div>
<script src="js/script.js"></script>
</body>

</html>