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

// フルネームの取得
$user_name = $_SESSION['account']['name'];

try {
    // 現在の日付を取得
    $currentDate = new DateTime();

    // アカウント管理用のPDOインスタンスを使用して、免許証の有効期限が14日以内に切れる従業員を取得
    $sql = '
    SELECT account_id, account_name01, account_name02, account_license_expiration_date
    FROM accounts
    WHERE DATE(account_license_expiration_date) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt = $pdoAccount->prepare($sql);
    $stmt->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();
    $expiringAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 車両管理用のPDOインスタンスを使用して、車検有効期限が14日以内に切れる車両を取得
    $sql_vehicle = '
    SELECT car_number_name, vehicle_inspection_day
    FROM vehicles
    WHERE DATE(vehicle_inspection_day) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt_vehicle = $pdoVehicles->prepare($sql_vehicle);
    $stmt_vehicle->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt_vehicle->execute();
    $expiringVehicles = $stmt_vehicle->fetchAll(PDO::FETCH_ASSOC);

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
            <p><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?> さんがログイン中です</p>

            <!-- 免許証有効期限が近い従業員を表示 -->
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
                    <p>※<?= $fullName ?>　免許証有効期限：<?= htmlspecialchars($formattedExpirationDate, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- 車検有効期限が近い車両を表示 -->
            <?php if (!empty($expiringVehicles)): ?>
                <h2>車検を更新してください</h2>
                <?php foreach ($expiringVehicles as $vehicle): ?>
                    <?php
                    // 車検有効期限をフォーマットする
                    $inspectionDate = new DateTime($vehicle['vehicle_inspection_day']);
                    $formattedInspectionDate = $inspectionDate->format('Y年m月d日');
                    
                    // 号車名を取得
                    $carNumberName = htmlspecialchars($vehicle['car_number_name'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <p>※<?= $carNumberName ?>号車　車検有効期限：<?= htmlspecialchars($formattedInspectionDate, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

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
                <a href="#">
                    <button>勤怠管理</button>
                </a>
                <a href="../vehicles/list.php">
                    <button>車両管理</button>
                </a>
            </div>
            
        </main>

    </body>
    
</html>