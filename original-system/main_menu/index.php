<?php
 $dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
 $user = 'root';
 $password = '';
 ?>
 
<!DOCTYPE html>
<html lang="ja">
<html>
    <head>
        <title>メインメニュー</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <header>
            <h1>メインメニュー</h1>
                <!-- パンクズナビ -->
                <!-- <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="../login/index.html">
                            <span itemprop="name">ログイン</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                </ol> -->
        </header>

        <main>
            <div class="container">
                <a href="#">
                    <button>日次集計</button>
                </a>
                <a href="#">
                    <button>月次集計</button>
                </a>
                <a href="../accounts/list.php">
                  <button>人事管理</button>
                </a>
                <a href="#">
                  <button>勤怠管理</button>
                </a>
                <a href="#">
                  <button>車両管理</button>
                </a>
            </div>
        </main>
    </body>
</html>