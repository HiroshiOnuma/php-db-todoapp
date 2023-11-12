<body class="page <?php echo $bodyClass; ?>">
    <header>
        <h1><?php echo $heading_title; ?></h1>
        <?php
        //  ページごとのヘッダーのナビゲーションメニューの表示切り替え
        if ($bodyClass != 'register-check-page' && $bodyClass != 'register-page' && $bodyClass != 'thanks-page' && $bodyClass != 'login-page') :
        ?>
            <button class="menu toggle-menu">
                <img src="img/menu-icon.png" alt="">
            </button>
            <div class="menu sp-menu">
                <div class="inner">
                    <button class="close-btn">
                        閉じる
                    </button>
                    <nav>
                        <a href="index.php">トップ</a>
                        <a href="site.php">業務現場一覧</a>
                        <a href="pictures.php">写真一覧</a>
                    </nav>
                </div>
            </div>
            <div class="menu pc-menu">
                <nav>
                    <a href="index.php">トップ</a>
                    <a href="site.php">業務現場一覧</a>
                    <a href="pictures.php">写真一覧</a>
                </nav>
            </div>
        <?php endif; ?>
    </header>