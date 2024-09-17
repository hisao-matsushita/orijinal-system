<?php
// セッションを開始
session_start();

// config.php をインクルードして、PDOインスタンスや定数を利用
require '../config/config.php';

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // セッションを初期化して破棄
    $_SESSION = [];
    session_destroy();

    // ログインページにリダイレクト
    header('Location: ../login/index.php');
    exit();
}

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // ログイン状態でない場合、ログインページにリダイレクト
    header('Location: ../login/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>人事管理</title>
        <link rel="stylesheet" href="menu.css">
    </head>

    <body>
        <header>
            <h1>人事管理トップ</h1>
            <!-- パンクズナビ -->
            <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="../main_menu/index.php">
                        <span itemprop="name">メインメニュー</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
            </ol>
        </header>

        <main>
            <div class="container">
                <a href="#">
                    <button>勤怠登録</button>
                </a>
                <a href="#">
                    <button>勤怠編集</button>
                </a>
                <a href="#">
                    <button>乗務員勤怠一覧</button>
                </a>
                <a href="#">
                    <button>内勤者勤怠一覧</button>
                </a>
                <a href="#">
                    <button>勤務表集計（日別）</button>
                </a>
                <a href="#">
                    <button>勤務表集計（月別）</button>
                </a>
                <a href="#">
                    <button>個別詳細</button>
                </a>
            </div>
            
        </main>

    </body>
    
</html>