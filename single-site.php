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

$stmt1 = $db->prepare('select * from sites where id=?');
if (!$stmt1) {
    die($db->error);
}
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt1->bind_param('i', $id);
$success1 = $stmt1->execute();
if (!$success1) {
    die($db->error);
}
$stmt1->bind_result($id, $user_id, $site_name, $site_address, $site_remarks);
$stmt1->store_result();
$stmt1->fetch();

$db = dbconnect();

// フォームの送信
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // タスクメッセージの投稿
    if (isset($_POST['message'])) {
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $stmt2 = $db->prepare('insert into tasks (user_id, site_id, message) values(?,?,?)');
        if (!$stmt2) {
            die($db->error);
        }
        $stmt2->bind_param('iis', $user_id, $id, $message);
        $success2 = $stmt2->execute();
        if (!$success2) {
            die($db->error);
        }
        header("Location: single-site.php?id=$id");
        exit();
    }

    // ファイル関連の取得
    $file = $_FILES['img'];
    $file_name = basename($file['name']);
    $tmp_path = $file['tmp_name'];
    $file_error = $file['error'];
    $file_size = $file['size'];
    $upload_dir = 'upload_img/';
    $save_filename = date('YmdHis') . '_' . $file_name;
    $err_msgs = [];
    $save_path = $upload_dir . $save_filename;
    var_dump($file);
    echo '<br>';

    // ファイルのバリデーション
    // ファイルサイズが1MB未満か
    if ($file_size > 1048576 || $file_error == 2) {
        array_push($err_msgs, 'file_size');
        echo '<br>';
    }

    // 拡張は画像形式か
    $allow_ext = array('jpg', 'jpeg', 'png');
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_ext), $allow_ext)) {
        array_push($err_msgs, 'file_image');
        echo '<br>';
    }

    if (count($err_msgs) === 0) {
        // ファイルはあるかどうか
        if (is_uploaded_file($tmp_path)) {
            if (move_uploaded_file($tmp_path, $save_path)) {
                echo $file_name . 'を' . $upload_dir . 'にアップロードしました。';
                echo '<br>';

                // DBに保存
                $stmt3 = $db->prepare('insert into file_data(user_id, site_id, file_name, file_path) values (?, ?, ?, ?)');
                if (!$stmt3) {
                    die($db->error);
                }
                $stmt3->bind_param('iiss', $user_id, $id, $file_name, $save_path);
                $success3 = $stmt3->execute();
                if (!$success3) {
                    die($db->error);
                }
                header("Location: single-site.php?id=$id");
                exit();
            } else {
                echo 'ファイルが保存できませんでした。';
                echo '<br>';
            }
        } else {
            echo 'ファイルが選択されていません。';
            echo '<br>';
        }
    } else {
        foreach ($err_msgs as $msg) {
            echo $msg;
            echo '<br>';
        }
    }
}

$title = '業務現場詳細 | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>

<?php
$bodyClass = 'single-site-page';
$heading_title = '業務現場詳細';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <?php include('includes/user_menu.php'); ?>
        <div class="container">
            <dl>
                <div class="wrapper site-name">
                    <dt>現場名: </dt>
                    <dd><?php echo h($site_name); ?></dd>
                </div>
                <div class="wrapper site-address">
                    <dt>住所: </dt>
                    <dd><?php echo h($site_address); ?></dd>
                </div>
                <div class="wrapper site-remarks">
                    <dt>備考: </dt>
                    <dd><?php echo h($site_remarks); ?></dd>
                </div>
            </dl>
        </div>
        <form action="" method="post" class="task-form" id="task-form">
            <dl>
                <dt class="form-heading task-form-heading"><?php echo h($site_name); ?>のタスクを入力</dt>
                <dd>
                    <textarea name="message" cols="30" rows="5" placeholder="タスクメッセージを入力してください"></textarea>
                </dd>
            </dl>
            <input type="submit" value="タスクを追加" class="form-btn">
        </form>
        <div class="container" id="tasks-container">
            <h2 class="tasks-heading"><?php echo h($site_name); ?>のタスク</h2>
            <ul class="tasks today-tasks">
                <?php
                $stmt4 = $db->prepare('select id, message from tasks where site_id=?');
                if (!$stmt4) {
                    die($db->error);
                }
                $stmt4->bind_param('i', $id);
                $success4 = $stmt4->execute();
                if (!$success4) {
                    die($db->error);
                }
                $stmt4->bind_result($task_id, $message);
                while ($stmt4->fetch()) : ?>
                    <li><input type="checkbox" id="task"><label for="task"><?php echo h($message); ?></label>
                        <a class="edit-btn" href="single-site_update.php?id=<?php echo h($task_id); ?>"><span class="material-symbols-outlined">
                                edit
                            </span>
                        </a>
                        <a class="delete-btn" href="delete.php?single-site_id=<?php echo h($task_id); ?>"><span class="material-symbols-outlined">
                                delete
                            </span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <div class="count">
                <!-- 現場詳細タスクの完了数を取得し表示 -->
                <?php
                $stmt5 = $db->prepare('select count(*) from tasks where site_id=?');
                if (!$stmt5) {
                    die($db->error);
                }
                $stmt5->bind_param('i', $id);
                $success5 = $stmt5->execute();
                if (!$success5) {
                    die($db->error);
                }
                $stmt5->store_result();
                $stmt5->bind_result($cnt);
                $stmt5->fetch();
                ?>
                <p><?php echo h($site_name); ?>の残りタスク数:<span><?php echo $cnt; ?></span></p>
            </div>
        </div>
        <div class="container pictures-container" id="pictures-container">
            <h2>写真を追加</h2>
            <form action="" method="post" class="picture-form" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                <input type="hidden" name="site_id" value=<?php echo $id; ?> />
                <input type="file" name="img">
                <div class="error-container">
                    <?php if (isset($err_msgs) && $err_msgs === 'file_size') : ?>
                        <p class="error">* ファイルサイズを1MB未満にしてください。</p>
                    <?php endif; ?>
                    <?php if (isset($err_msgs) && $err_msgs === 'file_image') : ?>
                        <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
                    <?php endif; ?>
                </div>
                <input type="submit" value="写真を追加" class="form-btn picture-form-btn">
            </form>
            <div class="container pictures-wrapper">
                <!-- ファイルデータを取得 -->
                <?php
                $stmt6 = $db->prepare('select id, file_name,file_path from file_data where site_id=?');
                if (!$stmt6) {
                    die($db->error);
                }
                $stmt6->bind_param('i', $id);
                $success6 = $stmt6->execute();
                if (!$success6) {
                    die($db->error);
                }
                $stmt6->store_result();
                $stmt6->bind_result($file_id, $file_name, $file_path);
                ?>
                <?php while ($stmt6->fetch()) : ?>
                    <div class="picture-item">
                        <img src="<?php echo $file_path; ?>" alt="">
                        <a class="delete-btn" href="delete.php?single-site-image_id=<?php echo h($file_id); ?>">
                            <span class="material-symbols-outlined">
                                delete
                            </span>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="nav">
    <div class="inner nav-area">
        <a href="#pictures-container">
            <button class="picture-btn">
                <span class="material-symbols-outlined">
                    image
                </span>
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