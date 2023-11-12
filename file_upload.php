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

// ファイルの保存
// DB接続
// DBへの保存

$db = dbconnect();

// ファイル関連の取得
$file = $_FILES['img'];
$file_name = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_error = $file['error'];
$file_size = $file['size'];
$upload_dir = 'upload_img/';
$save_filename = date('YmdHis') . $file_name;
$error_msgs = array();
$save_path = $upload_dir . $save_filename;

var_dump($file);
echo '<br>';

// ファイルのバリデーション
// ファイルサイズが1MB未満か
if ($file_size > 1048576 || $file_error == 2) {
    array_push($error_msgs, 'ファイルサイズを1MB未満にしてください。');
    echo '<br>';
}

// 拡張は画像形式か
$allow_ext = array('jpg', 'jpeg', 'png');
$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
if (!in_array(strtolower($file_ext), $allow_ext)) {
    array_push($error_msgs, '画像ファイルを添付してください。');
    echo '<br>';
}

if (count($error_msgs) === 0) {
    // ファイルがあるかどうか
    if (is_uploaded_file($tmp_path)) {
        if (move_uploaded_file($tmp_path, $save_path)) {
            echo $file_name . 'を' . $upload_dir . 'にアップしました。';
            echo '<br>';

            // $site_idを取得
            $site_id = filter_input(INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT);

            // DBに保存
            $stmt = $db->prepare('insert into file_data(user_id, site_id, file_name, file_path) values (?, ?, ?, ?)');
            if (!$stmt) {
                die($db->error);
            }
            $stmt->bind_param('iiss', $user_id, $site_id, $file_name, $save_path);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }
        } else {
            echo 'ファイルが保存できませんでした。';
        }
    } else {
        echo 'ファイルが選択されていません。';
        echo '<br>';
    }
} else {
    foreach ($error_msgs as $msg) {
        echo $msg;
        echo '<br>';
    }
}
?>
<a href="single-site.php?id=<?php echo $site_id; ?>">戻る</a>

<!-- ファイルデータを取得 -->
<?php
$stmt = $db->prepare('select file_name,file_path from file_data');
if (!$stmt) {
    die($db->error);
}
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($filename, $filepath);
while ($stmt->fetch()) :
?>
    <img src="<?php echo $filepath; ?>" alt="">
<?php endwhile; ?>