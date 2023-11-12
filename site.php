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

// 業務現場の投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = filter_input(INPUT_POST, 'site_name', FILTER_SANITIZE_STRING);
    $site_address = filter_input(INPUT_POST, 'site_address', FILTER_SANITIZE_STRING);
    $site_remarks = filter_input(INPUT_POST, 'site_remarks', FILTER_SANITIZE_STRING);

    $stmt = $db->prepare('insert into sites (site_name, site_address, site_remarks, user_id) values(?,?,?,?)');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('sssi', $site_name, $site_address, $site_remarks, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header('Location: site.php');
    exit();
}

$title = '業務現場一覧 | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'site-page';
$heading_title = '業務現場一覧';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>
        <form action="" method="post" class="site-form" id="site-form">
            <dl>
                <dt class="form-heading site-form-heading">業務現場を追加</dt>
                <dd>
                    <label for="site-name">現場の名称</label>
                    <input type="text" id="site-name" name="site_name">
                    <label for="site-address">住所</label>
                    <input type="text" id="site-address" name="site_address">
                    <label for="site_remarks">備考</label>
                    <textarea name="site_remarks" cols="30" rows="5"></textarea>
                </dd>
            </dl>
            <input type="submit" value="業務現場を追加" class="form-btn">
        </form>
        <div class="container" id="site-container">
            <h2>業務現場一覧</h2>
            <table class="sites">
                <tr>
                    <th>現場名</th>
                    <th>住所</th>
                    <th>編集/削除</th>
                </tr>
                <?php
                $stmt = $db->prepare('select id, site_name, site_address from sites where user_id=? order by id desc');
                if (!$stmt) {
                    die($db->error);
                }
                $stmt->bind_param('i', $user_id);
                $success = $stmt->execute();
                if (!$success) {
                    die($db->error);
                }
                $stmt->bind_result($id, $site_name, $site_address);
                while ($stmt->fetch()) :
                ?>
                    <tr>
                        <td><a href="single-site.php?id=<?php echo h($id); ?>"><?php echo h($site_name); ?></a></td>
                        <td><?php echo h($site_address); ?></td>
                        <td>
                            <a class="edit-btn" href="site_update.php?id=<?php echo h($id); ?>"><span class="material-symbols-outlined">
                                    edit
                                </span>
                            </a>
                            <a class="delete-btn" href="delete.php?site_id=<?php echo h($id); ?>">
                                <span class="material-symbols-outlined">
                                    delete
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
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