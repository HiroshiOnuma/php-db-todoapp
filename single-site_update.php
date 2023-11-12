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

$task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$task_id) {
    header("Location: index.php");
    exit();
}

$db = dbconnect();

// 現場詳細タスクメッセージの編集
$stmt = $db->prepare('select message from tasks where id=? and user_id=?');
if (!$stmt) {
    die($db->error);
}
$stmt->bind_param('ii', $task_id, $user_id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($message);
$result = $stmt->fetch();
if (!$result) {
    die('タスクメッセージの指定が正しくありません');
}
$stmt->close();

$title = '業務現場詳細タスク編集 | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'update-page';
$heading_title = '業務現場詳細';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>

        <form action="update_do.php" method="post" class="task-form" id="task-form">
            <dl>
                <dt class="form-heading task-form-heading">タスクメッセージを編集</dt>
                <dd>
                    <input type="hidden" name="id" value="<?php echo $task_id; ?>">
                    <textarea name="site_update_message" cols="30" rows="5" placeholder="タスクメッセージを入力してください"><?php echo h($message); ?>
                        </textarea>
                </dd>
            </dl>
            <input type="submit" value="タスクを編集" class="form-btn">
        </form>

    </div>
</div>
<div class="nav">
    <div class="inner nav-area">
        <a href="#tasks-container">
            <button class="tasks-btn">
                <img src="img/check.png" alt="">
            </button>
        </a>
        <button class="hamburger-btn">
            <img src="img/hamburger.png" alt="">
        </button>
        <a href="#task-form">
            <button class="add-btn">
                <img src="img/plus.png" alt="">
            </button>
        </a>
    </div>
</div>
<script src="js/script.js"></script>
</body>

</html>