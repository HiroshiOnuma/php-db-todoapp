<?php

require('library.php');
$db = dbconnect();

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
$site_message = filter_input(INPUT_POST, 'site_update_message', FILTER_SANITIZE_STRING);

// トップページのタスクメッセージ編集処理
if (isset($message)) {
    $stmt = $db->prepare('update tasks set message=? where id=?');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('si', $message, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header("Location: index.php");
    exit();
}

// 業務詳細ページのタスクメッセージ編集処理
if (isset($site_message)) {
    $stmt = $db->prepare('update tasks set message=? where id=?');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('si', $site_message, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    // $site_idをDBから取得し、サイトIDページへ移動
    $select_stmt = $db->prepare('select site_id from tasks where id=?');
    if (!$select_stmt) {
        die($db->error);
    }
    $select_stmt->bind_param('i', $id);
    $select_success = $select_stmt->execute();
    if (!$select_success) {
        die($db->error);
    }
    $select_stmt->bind_result($site_id);
    $select_stmt->fetch();
    if ($site_id) {
        header("Location: single-site.php?id=$site_id");
        exit();
    } else {
        echo "サイトIDが見つかりませんでした。";
    }
}
