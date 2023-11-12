<?php
$image = $_FILES['image'];
if (
    $image['type'] === 'image/jpeg' ||
    $image['type'] === 'image/png'
) {
    $path = 'img/' . $image['name'];
    $success = move_uploaded_file($image['tmp_name'], $path);
    if ($success) {
        echo '成功しました';
    } else {
        echo '失敗しました';
    }
} else {
    echo 'ファイル形式が不正です';
}
