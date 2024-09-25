<?php
session_start();
// ユーザーがログインしているか確認
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
//     header("Location: ../login/index.php");
//     exit;
// }
require '../common/config.php';

$processedAccounts = [];
$logged_in_workclass = $_SESSION['account']['workclass'] ?? null; // ログインユーザーの勤務区分

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>人事管理メニュー</title>
        <link rel="stylesheet" href="menu.css">
    </head>

    <body>
        <header>
            <h1>人事管理メニュー</h1>
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
                <!-- 1段目: 勤怠登録と勤怠編集 -->
                <a href="#"><button>勤怠登録</button></a>
                <a href="#"><button>勤怠編集</button></a>

                <!-- 2段目: 乗務員勤怠一覧と内勤者勤怠一覧 -->
                <a href="#"><button>乗務員勤怠一覧</button></a>
                <a href="#"><button>内勤者勤怠一覧</button></a>

                <!-- 3段目: 勤務表集計（日別）と勤務表集計（月別） -->
                <a href="#"><button>勤務表集計<br>（日別）</button></a>
                <a href="#"><button>勤務表集計<br>（月別）</button></a>

                <!-- 4段目: 個別詳細 -->
                <a href="#"><button>個別詳細</button></a>
            </div>
        </main>

    </body>

</html>