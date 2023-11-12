<?php
require('library.php');
$db = dbconnect();

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$site_name = filter_input(INPUT_POST, 'site_name', FILTER_SANITIZE_STRING);
$site_address = filter_input(INPUT_POST, 'site_address', FILTER_SANITIZE_STRING);
$site_remarks = filter_input(INPUT_POST, 'site_remarks', FILTER_SANITIZE_STRING);

$stmt = $db->prepare('update sites set site_name=?, site_address=?, site_remarks=? where id=?');
if(!$stmt) {
    die($db->error);
}
$stmt->bind_param('sssi', $site_name, $site_address, $site_remarks, $id);
$success = $stmt->execute();
if(!$success) {
    die($db->error);
}
header("Location: site.php");
exit();
