<div class="user-menu">
    <!-- ユーザーのアイコンをDBから取得 -->
    <?php
    $user_icon_stmt = $db->prepare('select picture from users where id=?');
    if (!$user_icon_stmt) {
        die($db->error);
    }
    $user_icon_stmt->bind_param('i', $user_id);
    $user_icon_result = $user_icon_stmt->execute();
    if (!$user_icon_result) {
        die($db->error);
    }
    $user_icon_stmt->bind_result($user_picture);
    $user_icon_stmt->fetch();
    $user_icon_stmt->close();
    ?>
    <div class="profile">
        <?php if ($user_picture) : ?>
            <img src="users_picture/<?php echo h($user_picture); ?>" alt="">
        <?php endif; ?>
    </div>
    <a href="logout.php">ログアウト</a>
</div>