<?php
session_start();
require '../config/config.php';  // config.php で既に PDO インスタンスが生成されている

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;

// 日付処理用
$currentDate = new DateTime();
$nextMonth = (clone $currentDate)->modify('+1 month');

try {
    // 検索条件を取得
    $keyword = $_GET['keyword'] ?? '';

    // ソート条件の取得（デフォルトは車番の昇順）
    $sort = $_GET['sort'] ?? 'car_number_name';
    $order = $_GET['order'] ?? 'asc';
    
    // ソート対象のカラム名を制限
    $allowed_sort_columns = ['car_number_name', 'car_model', 'vehicle_inspection_day', 'first_registration_day', 'vehicle_updateday'];
    if (!in_array($sort, $allowed_sort_columns)) {
        $sort = 'car_number_name';
    }

    // 昇順・降順の指定
    $order = ($order === 'desc') ? 'desc' : 'asc';

    // 基本のSQL文
    $sql = "SELECT * FROM vehicles";

    // フィルタリングの追加
    $filter = $_GET['filter'] ?? '';
    $params = [];  // バインドするパラメータを配列にまとめておく

    if ($filter === 'inspection') {
        // 車検有効期限が翌月のデータを表示
        $sql .= " WHERE vehicle_inspection_day BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $nextMonth->format('Y-m-01');
        $params[':end_date'] = $nextMonth->format('Y-m-t');
    } elseif ($filter === 'meter') {
        // メーター検査が翌月のデータを表示
        $sql .= " WHERE meter_inspection_day BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $nextMonth->format('Y-m-01');
        $params[':end_date'] = $nextMonth->format('Y-m-t');
    } elseif (!empty($keyword)) {
        // キーワード検索の追加
        $sql .= " WHERE REPLACE(car_number, '-', '') LIKE :keyword";
        $params[':keyword'] = "%" . str_replace('-', '', $keyword) . "%";
    }

    // ソートの追加
    $sql .= " ORDER BY $sort $order";

    // SQLクエリの実行準備
    $stmt = $pdoVehicles->prepare($sql);

    // パラメータをバインド
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
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

// 車両番号のフォーマット関数
function formatCarNumberPart($part, $is_purpose = false) {
    if (empty($part)) {
        return $is_purpose ? '･' : '･･';  // 用途番号は「･」それ以外は「･･」
    } elseif (strlen($part) === 1) {
        return '･' . $part;  // 1桁の場合は「･」を付ける
    }
    return $part;
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

                <div class="filter-buttons">
                    <button type="submit" name="filter" value="inspection">車検</button>
                    <button type="submit" name="filter" value="meter">メーター検査</button>
                </div>
            </form>

            <div class="print-area">
                <table class="list">
                    <tr>
                        <th></th>
                        <th class="car_number_name">車番
                            <a href="?sort=car_number_name&order=asc">▲</a>
                            <a href="?sort=car_number_name&order=desc">▼</a>
                        </th>
                        <th class="car_model">車種
                            <a href="?sort=car_model&order=asc">▲</a>
                            <a href="?sort=car_model&order=desc">▼</a>
                        </th>
                        <th class="car_">登録番号</th>
                        <th class="vehicle_inspection">車検有効期限
                            <a href="?sort=vehicle_inspection_day&order=asc">▲</a>
                            <a href="?sort=vehicle_inspection_day&order=desc">▼</a>
                        </th>
                        <th class="first_registration">初年度登録年月
                            <a href="?sort=first_registration_day&order=asc">▲</a>
                            <a href="?sort=first_registration_day&order=desc">▼</a>
                        </th>
                        <th class="vehicle_updateday">更新年月日
                            <a href="?sort=vehicle_updateday&order=asc">▲</a>
                            <a href="?sort=vehicle_updateday&order=desc">▼</a>
                        </th>
                    </tr>

                    <!-- 車両リストの表示部分 -->
                    <?php foreach ($finalVehicles as $vehicle): ?> 
                        <tr>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <a href="<?= htmlspecialchars($vehicle['detail_link'], ENT_QUOTES, 'UTF-8') ?>" class="button">詳細</a>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?= htmlspecialchars($vehicle['car_number_name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?= htmlspecialchars($vehicle['car_model'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?php
                                // 車両番号の各部分をフォーマット
                                $transpottaition = formatCarNumberPart($vehicle['car_transpottaition']);
                                $classification_no = formatCarNumberPart($vehicle['car_classification_no']);
                                $purpose = formatCarNumberPart($vehicle['car_purpose'], true);
                                $car_number_parts = explode('-', $vehicle['car_number']);
                                $number01 = formatCarNumberPart($car_number_parts[0] ?? '');
                                $number02 = formatCarNumberPart($car_number_parts[1] ?? '');
                                echo htmlspecialchars($transpottaition . ' ' . $classification_no . ' ' . $purpose . ' ' . $number01 . '-' . $number02, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?php
                                $inspectionYear = date('Y', strtotime($vehicle['vehicle_inspection_day']));
                                $inspectionMonthDay = date('m月d日', strtotime($vehicle['vehicle_inspection_day']));
                                echo htmlspecialchars(convertToJapaneseEra($inspectionYear) . $inspectionMonthDay, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?php
                                $firstRegYear = date('Y', strtotime($vehicle['first_registration_day']));
                                $firstRegMonth = date('m月', strtotime($vehicle['first_registration_day']));
                                echo htmlspecialchars(convertToJapaneseEra($firstRegYear) . $firstRegMonth, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                            <td class="<?= $vehicle['is_suspended'] ? 'suspended' : '' ?>">
                                <?php
                                $updatedYear = date('Y', strtotime($vehicle['vehicle_updateday']));
                                $updatedMonthDay = date('m月d日', strtotime($vehicle['vehicle_updateday']));
                                echo htmlspecialchars(convertToJapaneseEra($updatedYear) . $updatedMonthDay, ENT_QUOTES, 'UTF-8');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>

        </main>

    </body>

</html>