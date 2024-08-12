<?php
 $dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
 $user = 'root';
 $password = '';
 try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 現在の日付を取得
    $currentDate = new DateTime();

    // 14日以内に有効期限が切れる免許証を持つ従業員を取得
    $sql = '
        SELECT account_id, account_name01, account_name02, account_license_expiration_date_year, account_license_expiration_date_month, account_license_expiration_date_day
        FROM accounts
        WHERE DATE(CONCAT(account_license_expiration_date_year, "-", account_license_expiration_date_month, "-", account_license_expiration_date_day)) BETWEEN :current_date AND DATE_ADD(:current_date, INTERVAL 14 DAY)
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':current_date', $currentDate->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();

    $expiringAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}
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
       
        <?php if (!empty($expiringAccounts)): ?>
    <h2>免許証を更新してください</h2>
    <?php foreach ($expiringAccounts as $account): ?>
        <?php
            // 有効期限をフォーマットする
            $expirationDate = "{$account['account_license_expiration_date_year']}年{$account['account_license_expiration_date_month']}月{$account['account_license_expiration_date_day']}日";
            
            // 氏名をフォーマットする
            $fullName = htmlspecialchars($account['account_name01'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($account['account_name02'], ENT_QUOTES, 'UTF-8');
        ?>
        <p>※<?= $fullName ?>　免許証有効期限：<?= htmlspecialchars($expirationDate, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
<?php endif; ?>
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