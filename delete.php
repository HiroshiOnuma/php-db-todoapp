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

// トップページのタスクメッセージの削除
if (isset($_GET['id'])) {
    $task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$task_id) {
        header("Location: index.php");
        exit();
    }
    $stmt = $db->prepare('delete from tasks where id=? and user_id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('ii', $task_id, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header("Location: index.php");
    exit();
}

// 業務現場一覧ページの現場を削除
if (isset($_GET['site_id'])) {
    $site_id = filter_input(INPUT_GET, 'site_id', FILTER_SANITIZE_NUMBER_INT);
    if (!$site_id) {
        header("Location: site.php");
        exit();
    }
    $stmt = $db->prepare('delete from sites where id=? and user_id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('ii', $site_id, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header("Location: site.php");
    exit();
}

// 業務詳細ページのタスクメッセージを削除
if (isset($_GET['single-site_id'])) {
    $site_task_id = filter_input(INPUT_GET, 'single-site_id', FILTER_SANITIZE_NUMBER_INT);
    if (!$site_task_id) {
        header("Location: site.php");
        exit();
    }

    // $site_idをDBから取得
    $select_stmt = $db->prepare('select site_id from tasks where id=?');
    if (!$select_stmt) {
        die($db->error);
    }
    $select_stmt->bind_param('i', $site_task_id);
    $select_success = $select_stmt->execute();
    if (!$select_success) {
        die($db->error);
    }
    $select_stmt->bind_result($site_id);
    $select_stmt->fetch();
    $select_stmt->close(); // 結果を閉じる

    // 現場詳細タスクの削除後に、サイトIDページへ移動
    $stmt = $db->prepare('delete from tasks where id=? and user_id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('ii', $site_task_id, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    if ($site_id) {
        // サイトのsite_idを使ってリダイレクト
        header("Location: single-site.php?id=$site_id");
        exit();
    } else {
        echo "サイトIDが見つかりませんでした。";
    }
}

// 業務詳細ページの画像を削除
if(isset($_GET['single-site-image_id'])) {
    $single_site_image_id = filter_input(INPUT_GET, 'single-site-image_id', FILTER_SANITIZE_NUMBER_INT);
    if (!$single_site_image_id) {
        header("Location: site.php");
        exit();
    }

    // $site_idをDBから取得
    $select_stmt = $db->prepare('select site_id, file_path from file_data where id=?');
    if (!$select_stmt) {
        die($db->error);
    }
    $select_stmt->bind_param('i', $single_site_image_id);
    $select_success = $select_stmt->execute();
    if (!$select_success) {
        die($db->error);
    }
    $select_stmt->bind_result($site_id, $file_path);
    $select_stmt->fetch();
    $select_stmt->close(); // 結果を閉じる

    // 現場詳細画像の削除後に、サイトIDページへ移動
    $stmt = $db->prepare('delete from file_data where id=? and user_id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('ii', $single_site_image_id, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // ディレクトリからファイルを削除
    unlink($file_path);

    if ($site_id) {
        // サイトのsite_idを使ってリダイレクト
        header("Location: single-site.php?id=$site_id");
        exit();
    } else {
        echo "サイトIDが見つかりませんでした。";
    }
}

// 写真一覧ページから画像を削除
if(isset($_GET['pictures_image_id'])) {
    $pictures_image_id = filter_input(INPUT_GET, 'pictures_image_id', FILTER_SANITIZE_NUMBER_INT);
    if (!$pictures_image_id) {
        header("Location: site.php");
        exit();
    }

    // $file_pathをDBから取得
    $select_stmt = $db->prepare('select file_path from file_data where id=?');
    if (!$select_stmt) {
        die($db->error);
    }
    $select_stmt->bind_param('i', $pictures_image_id);
    $select_success = $select_stmt->execute();
    if (!$select_success) {
        die($db->error);
    }
    $select_stmt->bind_result($file_path);
    $select_stmt->fetch();
    $select_stmt->close(); // 結果を閉じる

    // 写真一覧の画像の削除後に、写真一覧ページへ移動
    $stmt = $db->prepare('delete from file_data where id=? and user_id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('ii', $pictures_image_id, $user_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // ディレクトリからファイルを削除
    unlink($file_path);

    // 写真一覧ページへリダイレクト
    header("Location: pictures.php");
}
