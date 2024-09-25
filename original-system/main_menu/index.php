<?php
// セッションを開始
session_start();
// ユーザーがログインしているか確認
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
//     header("Location: ../login/index.php");
//     exit;
// }
// config.php をインクルードして、PDOインスタンスや定数を利用
require '../common/config.php';
// セッション管理をインクルード
require '../common/session_manager.php';  // セッションタイムアウト管理

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['account']['id'])) {
        // last_login_time を NULL にするクエリ
        $sql = 'UPDATE accounts SET last_login_time = NULL WHERE account_id = :account_id';
        $stmt = $pdoAccount->prepare($sql);
        $stmt->bindValue(':account_id', $_SESSION['account']['id'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // last_login_timeが正しくNULLに設定されたか確認するためのデバッグ
            $sql_check = 'SELECT account_id, last_login_time FROM accounts WHERE account_id = :account_id';
            $stmt_check = $pdoAccount->prepare($sql_check);
            $stmt_check->bindValue(':account_id', $_SESSION['account']['id'], PDO::PARAM_INT);
            $stmt_check->execute();
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['last_login_time'] === null) {
                // last_login_time がNULLであれば成功
                echo 'ログアウト成功: last_login_time は NULL になっています。';
            } else {
                // last_login_time がNULLでなければ問題あり
                echo 'ログアウト失敗: last_login_time が NULL ではありません。';
                error_log("ログアウト処理に失敗しました: アカウントID " . $_SESSION['account']['id']);
            }

            // セッションを初期化して破棄
            session_unset(); // すべてのセッション変数をクリア
            session_destroy(); // セッションを破棄
        } else {
            // エラーログを追加
            error_log("ログアウト処理に失敗しました: アカウントID " . $_SESSION['account']['id']);
        }
    }

    // ログインページにリダイレクト
    header('Location: ../login/index.php');
    exit();
}

// フルネームの取得
$user_name = $_SESSION['account']['name'];

try {
    // 現在の日付を取得
    $currentDate = new DateTime();

    // 免許証の有効期限が14日以内に切れる従業員を取得
    $sql = '
    SELECT account_id, account_name01, account_name02, account_license_expiration_date
    FROM accounts
    WHERE DATE(account_license_expiration_date) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt = $pdoAccount->prepare($sql);
    $stmt->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();
    $expiringAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 車検有効期限が14日以内に切れる車両を取得
    $sql_vehicle = '
    SELECT car_number_name, vehicle_inspection_day
    FROM vehicles
    WHERE DATE(vehicle_inspection_day) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt_vehicle = $pdoAccount->prepare($sql_vehicle);
    $stmt_vehicle->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt_vehicle->execute();
    $expiringVehicles = $stmt_vehicle->fetchAll(PDO::FETCH_ASSOC);

    // ログインしてから15分以内のアクティブなユーザーを取得
    $inactive_limit = 900; // 15分 = 900秒
    $sql_logged_in_users = '
        SELECT account_name01, account_name02
        FROM accounts
        WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_login_time) < :inactive_limit
    ';
    $stmt_logged_in = $pdoAccount->prepare($sql_logged_in_users);
    $stmt_logged_in->bindValue(':inactive_limit', $inactive_limit, PDO::PARAM_INT);
    $stmt_logged_in->execute();
    $logged_in_users = $stmt_logged_in->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>メインメニュー</title>
        <link rel="stylesheet" href="index.css">
    </head>

    <body>
        <header>
            <h1>メインメニュー</h1>
            <div class="logout-button">
                <a href="?action=logout">
                    <button>ログアウト</button>
                </a>
            </div>
        </header>

        <main>
            <!-- ログインユーザーの氏名を表示 -->
            <!-- <p><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?> さんがログイン中です</p> -->

            <!-- アクティブなログイン中のユーザーを表示 -->
            <div class="left-align-section">
            <?php if (!empty($logged_in_users)): ?>
                <h2>ログイン中のユーザー</h2>
                <?php foreach ($logged_in_users as $user): ?>
                    <p class="first-style"><?= htmlspecialchars($user['account_name01'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($user['account_name02'], ENT_QUOTES, 'UTF-8') ?> さん</p>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>

            <!-- 免許証有効期限が近い従業員を表示 -->
            <div class="vertical-container">
            <?php if (!empty($expiringAccounts)): ?>
                <h2>免許証を更新してください</h2>
                <?php foreach ($expiringAccounts as $account): ?>
                    <?php
                    // 有効期限をフォーマットする
                    $expirationDate = new DateTime($account['account_license_expiration_date']);
                    $formattedExpirationDate = $expirationDate->format('Y年m月d日');
                    
                    // 氏名をフォーマットする
                    $fullName = htmlspecialchars($account['account_name01'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($account['account_name02'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <p class="second-style">※<?= $fullName ?>　免許証有効期限：<?= htmlspecialchars($formattedExpirationDate, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- 車検有効期限が近い車両を表示 -->
            <?php if (!empty($expiringVehicles)): ?>
                <h3>車検を更新してください</h3>
                <?php foreach ($expiringVehicles as $vehicle): ?>
                    <?php
                    // 車検有効期限をフォーマットする
                    $inspectionDate = new DateTime($vehicle['vehicle_inspection_day']);
                    $formattedInspectionDate = $inspectionDate->format('Y年m月d日');
                    
                    // 号車名を取得
                    $carNumberName = htmlspecialchars($vehicle['car_number_name'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <p class="second-style">※<?= $carNumberName ?>号車　車検有効期限：<?= htmlspecialchars($formattedInspectionDate, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <!-- メニュー -->
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
                <a href="../attendance/menu.php">
                    <button>勤怠管理</button>
                </a>
                <a href="../vehicles/list.php">
                    <button>車両管理</button>
                </a>
            </div>
            
        </main>

    </body>
    
</html>