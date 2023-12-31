<?php
session_start();
require('../library.php');

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];
}
$error = [];

// フォームの内容をチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    if ($form['name'] === '') {
        $error['name'] = 'blank';
    }

    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error['email'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('SELECT count(*) from users where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $form['email']);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt->bind_result($cnt);
        $stmt->fetch();
        if ($cnt > 0) {
            $error['email'] = 'duplicate';
        }
    }

    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    } elseif (strlen($form['password']) < 4) {
        $error['password'] = 'length';
    }

    // 画像のチェック
    $image = $_FILES['image'];
    if ($image['name'] !== ''  && $image['error'] === 0) {
        $finfo = new finfo();

        $type = $finfo->file($image['tmp_name'], FILEINFO_MIME_TYPE);
        if ($type !== 'image/png' && $type !== 'image/jpeg') {
            $error['image'] = 'type';
        }
    }
    if (empty($error)) {
        $_SESSION['form'] = $form;

        // 画像のアップロード
        if ($image['name'] !== '') {
            $filename = date('YmdHis') . '_' . $image['name'];
            if (!move_uploaded_file($image['tmp_name'], '../users_picture/' . $filename)) {
                die('ファイルのアップロードに失敗しました');
            }
            $_SESSION['form']['image'] = $filename;
        } else {
            $_SESSION['form']['image'] = '';
        }
        header('Location: check.php');
        exit();
    }
}
$title = 'ユーザー登録 | タスク管理アプリ';
$path = '../';
include $path . 'includes/head.php';
?>
</head>

<?php
$bodyClass = 'register-page';
$heading_title = 'ユーザー登録';
include $path . 'includes/header.php';
?>

<div class="inner">
    <div class="content">
        <form action="" method="post" enctype="multipart/form-data">
            <dl>
                <dt>ユーザー名<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="name" value="<?php echo h($form['name']); ?>">
                    <?php if (isset($error['name']) && $error['name'] === 'blank') : ?>
                        <p class="error">* ユーザー名を入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>メールアドレス<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="email" value="<?php echo h($form['email']); ?>">
                    <?php if (isset($error['email']) && $error['email'] === 'blank') : ?>
                        <p class="error">* メールアドレスを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['email']) && $error['email'] === 'duplicate') : ?>
                        <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード<span class="required">必須</span></dt>
                <dd>
                    <input type="password" name="password" value="<?php echo h($form['password']); ?>">
                    <?php if (isset($error['password']) && $error['password'] === 'blank') : ?>
                        <p class="error">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
                        <p class="error">* パスワードは4文字以上で入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>プロフィール画像</dt>
                <dd>
                    <input type="file" name="image">
                    <?php if (isset($error['image']) && $error['image'] === 'type') : ?>
                        <p class="error">* プロフィール画像は「.png」または「.jpg」の画像を指定してください</p>
                    <?php endif; ?>
                </dd>
            </dl>
            <div><input type="submit" class="form-btn confirm-btn" value="入力内容を確認する" /></div>
        </form>
    </div>
</div>
</body>

</html>