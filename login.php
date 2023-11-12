<?php
session_start();
require('library.php');
$error = [];
$email = '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($email === '' || $password === '') {
        $error['login'] = 'blank';
    } else {
        // ログインチェック
        $db = dbconnect();
        $stmt = $db->prepare('select id, user_name, password from users where email=? limit 1');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $email);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt->bind_result($id, $name, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            // ログイン成功
            session_regenerate_id();
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    }
}

$title = 'ログイン | タスク管理アプリ';
$path = './';
include $path . 'includes/head.php';
?>
</head>

<?php
$bodyClass = 'login-page';
$heading_title = 'ログイン';
include $path . 'includes/header.php';
?>
<div class="inner">
    <div class="content">
        <div id="lead">
            <p>メールアドレスとパスワードを記入してログインしてください。</p>
            <p>ユーザー登録がまだの方はこちらからどうぞ。</p>
            <a href="join/">ユーザー登録をする</a>
        </div>
        <form action="" method="post">
            <dl>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="email" value="<?php echo h($email); ?>">
                    <?php if (isset($error['login']) && $error['login'] === 'blank') : ?>
                        <p class="error">* メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if (isset($error['login']) && $error['login'] === 'failed') : ?>
                        <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    <input type="password" name="password" value="<?php echo h($password); ?>">
                </dd>
            </dl>
            <div>
                <input type="submit" value="ログイン" class="form-btn" />
            </div>
        </form>
    </div>
</div>
</body>

</html>