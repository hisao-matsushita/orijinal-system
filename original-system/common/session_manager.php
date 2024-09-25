<?php
// ユーザーがログインしているか確認
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
//     header("Location: ../login/index.php");
// }
// セッションを開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// セッションタイムアウト時間の設定（例：15分）
$inactive = 900; // 15分（900秒）

// セッションタイムアウトを確認
if (isset($_SESSION['timeout'])) {
    $session_life = time() - $_SESSION['timeout'];
    if ($session_life > $inactive) {
        // セッションの破棄
        session_unset();
        session_destroy();
        header("Location: ../login/index.php"); // タイムアウト後はログインページにリダイレクト
        exit;
    }
}

// セッションの最終アクティブ時間を更新
$_SESSION['timeout'] = time();
?>