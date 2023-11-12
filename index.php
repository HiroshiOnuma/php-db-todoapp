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

// タスクメッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $stmt = $db->prepare('insert into tasks (message, user_id) values(?,?)');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('si', $message, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header('Location: index.php');
    exit();
}

$title = 'タスク管理アプリ トップ';
$path = './';
include $path . 'includes/head.php';
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'top-page';
$heading_title = 'タスク管理アプリ';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>
        <form action="" method="post" class="task-form" id="task-form">
            <dl>
                <dt class="form-heading task-form-heading">本日のタスクを入力</dt>
                <dd>
                    <textarea name="message" cols="30" rows="5" placeholder="タスクメッセージを入力してください"></textarea>
                </dd>
            </dl>
            <input type="submit" value="タスクを追加" class="form-btn">
        </form>
        <div class="container" id="tasks-container">
            <h2 class="tasks-heading">本日(<?php echo date('Y年m月d日'); ?>)のタスク</h2>
            <ul class="tasks today-tasks">
                <?php
                $stmt = $db->prepare('select id, message from tasks where user_id=? order by id desc');
                if (!$stmt) {
                    die($db->error);
                }
                $stmt->bind_param('i', $user_id);
                $success = $stmt->execute();
                if (!$success) {
                    die($db->error);
                }
                $stmt->bind_result($id, $message);
                while ($stmt->fetch()) :
                    // 一意のidを生成
                    $unique_id = 'task_' . $id;
                ?>
                    <li><input type="checkbox" id="<?php echo $unique_id; ?>"><label for="<?php echo $unique_id; ?>"><?php echo h($message); ?></label>
                        <a href="update.php?id=<?php echo h($id); ?>" class="edit-btn"><span class="material-symbols-outlined">
                                edit
                            </span>
                        </a>
                        <a href="delete.php?id=<?php echo h($id); ?>" class="delete-btn"><span class="material-symbols-outlined">
                                delete
                            </span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <div class="count">
                <?php
                $stmt = $db->prepare('select count(*) from tasks where user_id=?');
                if (!$stmt) {
                    die($db->error);
                }
                $stmt->bind_param('i', $user_id);
                $success = $stmt->execute();
                if (!$success) {
                    die($db->error);
                }
                $stmt->bind_result($cnt);
                $stmt->fetch();
                ?>
                <p>本日(<?php echo date('Y年m月d日'); ?>)の残りタスク数:<span><?php echo h($cnt); ?></span></p>
            </div>
        </div>
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