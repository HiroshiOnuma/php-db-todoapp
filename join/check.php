<?php
session_start();
require('../library.php');
if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbconnect();
    $stmt = $db->prepare('insert into users (user_name, email, password, picture) VALUES (?, ?, ?, ?)');
    if (!$stmt) {
        die($db->error);
    }
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('ssss', $form['name'], $form['email'], $password, $form['image']);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    unset($_SESSION['form']);
    header('Location: thanks.php');
    exit();
}

$title = 'ユーザー登録確認 | タスク管理アプリ';
$path = '../';
include $path . 'includes/head.php';
?>
</head>

<?php
$bodyClass = 'register-check-page';
$heading_title = 'ユーザー登録';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <p>記入した内容を確認して、「登録する」ボタンをクリックしてください。</p>
        <form action="" method="post">
            <dl>
                <dt>ユーザー名</dt>
                <dd><?php echo h($form['name']); ?></dd>
                <dt>メールアドレス</dt>
                <dd><?php echo h($form['email']); ?></dd>
                <dt>パスワード</dt>
                <dd>【表示されません】</dd>
                <dt>プロフィール画像</dt>
                <dd class="profile-icon">
                    <img src="../users_picture/<?php echo h($form['image']); ?>" alt="">
                </dd>
            </dl>
            <div class="register-area">
                <a href="index.php?action=rewrite" class="back-btn">&laquo;&nbsp;戻る</a>
                <input type="submit" value="登録する" class="register-btn">
            </div>
        </form>
    </div>
</div>
</body>

</html>