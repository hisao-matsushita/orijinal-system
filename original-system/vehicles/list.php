<?php
session_start();
require '../config/config.php';

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;

try {
    // データベース接続
    $pdo = new PDO($dsnVehicles, $userVehicles, $passwordVehicles);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 検索条件を取得
    $keyword = $_GET['keyword'] ?? '';

    // 基本のSQL文
    $sql = "SELECT * FROM vehicless";
    
    // キーワード検索の追加
    if (!empty($keyword)) {
        $sql .= " WHERE CONCAT(car_number01, car_number02) LIKE :keyword";
    }
    
    // 車番の降順でデータを取得
    $sql .= " ORDER BY car_number_name ASC";

    // SQLクエリの実行準備
    $stmt = $pdo->prepare($sql);

    // キーワードがあればバインド
    if (!empty($keyword)) {
        $partial_match = "%{$keyword}%";
        $stmt->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
    }

    // クエリ実行
    $stmt->execute();
    $processedVehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 詳細リンクの生成
    $finalVehicles = [];
    foreach ($processedVehicles as $vehicle) {
        $vehicle['detail_link'] = "update.php?car_id=" . htmlspecialchars($vehicle['car_id'], ENT_QUOTES, 'UTF-8');
        $finalVehicles[] = $vehicle;
    }

} catch (PDOException $e) {
    echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit();
}

// 日本の元号変換関数
function convertToJapaneseEra($year) {
    $eras = [
        ['name' => '令和', 'start' => 2019],
        ['name' => '平成', 'start' => 1989],
    ];

    foreach ($eras as $era) {
        if ($year >= $era['start']) {
            $eraYear = $year - $era['start'] + 1;
            return $era['name'] . ($eraYear === 1 ? '元' : $eraYear) . '年';
        }
    }
    return $year . '年';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>車両一覧</title>
    <link rel="stylesheet" href="list.css">
</head>
<body>
    <header>
        <h1>車両一覧</h1>
        <nav>
            <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="../main_menu/index.php">
                        <span itemprop="name">メインメニュー</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
            </ol>
        </nav>
    </header>

    <main>
    <?php if (isset($logged_in_workclass) && ($logged_in_workclass === 1 || $logged_in_workclass === 2)): ?>
            <div class="insert">
                <a href="register.php" class="btn1">新規登録</a>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['message'])): ?>
            <p class="success-message"><?= htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="" method="get" class="search-form">
            <table class="search">
                <tr>
                    <th colspan="1">車両検索</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" placeholder="ナンバー検索" name="keyword" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                </tr>
            </table>
            <div class="btn_area">
                <button type="submit">検索</button>
                <button type="reset">クリア</button>
                <button type="button" onclick="window.print()">印刷</button>
            </div>
        </form>

        <div class="print-area">
            <table class="list">
                <tr>
                    <th></th>
                    <th class="car_number_name">車番</th>
                    <th class="car_model">車種</th>
                    <th class="car_">登録番号</th>
                    <th class="vehicle_inspection">車検有効期限</th>
                    <th class="first_registration">初年度登録年月</th>
                    <th class="vehicle_updateday">更新年月日</th>
                </tr>
                <!-- 車両リストの表示部分 -->
                <?php foreach ($finalVehicles as $vehicle): ?> 
    <tr>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>"><a href="<?= htmlspecialchars($vehicle['detail_link'], ENT_QUOTES, 'UTF-8') ?>" class="button">詳細</a></td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>"><?= htmlspecialchars($vehicle['car_number_name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>"><?= htmlspecialchars($vehicle['car_model'], ENT_QUOTES, 'UTF-8') ?></td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
            <?= htmlspecialchars($vehicle['car_transpottaition'], ENT_QUOTES, 'UTF-8') . ' ' . 
                htmlspecialchars($vehicle['car_classification_no'], ENT_QUOTES, 'UTF-8') . ' ' . 
                htmlspecialchars($vehicle['car_purpose'], ENT_QUOTES, 'UTF-8') . ' ' . 
                htmlspecialchars($vehicle['car_number01'], ENT_QUOTES, 'UTF-8') . '-' . 
                htmlspecialchars($vehicle['car_number02'], ENT_QUOTES, 'UTF-8') ?>
        </td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
            <?= htmlspecialchars(convertToJapaneseEra($vehicle['vehicle_inspection_year']), ENT_QUOTES, 'UTF-8') . 
                htmlspecialchars($vehicle['vehicle_inspection_month'], ENT_QUOTES, 'UTF-8') . '月' . 
                htmlspecialchars($vehicle['vehicle_inspection_day'], ENT_QUOTES, 'UTF-8') . '日' ?>
        </td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
            <?= htmlspecialchars(convertToJapaneseEra($vehicle['first_registration_year']), ENT_QUOTES, 'UTF-8') . 
                htmlspecialchars($vehicle['first_registration_month'], ENT_QUOTES, 'UTF-8') . '月' ?>
        </td>
        <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
            <?= htmlspecialchars(date('Y年m月d日', strtotime($vehicle['vehicle_updateday'])), ENT_QUOTES, 'UTF-8') ?>
        </td>
    </tr>
<?php endforeach ?>
            </table>
        </div>
    </main>
</body>
</html>