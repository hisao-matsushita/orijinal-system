<?php
session_start();
$errors = []; 
require '../config/config.php'; 
require '../config/validation.php';  

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;

// 日付を分割する関数
function splitDate($date) {
    return $date ? explode('-', $date) : ['', '', ''];
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
    return $year;
}

// 和暦の年数オプション生成関数
function generateJapaneseYearOptions($startYear, $endYear, $selectedYear) {
    $options = '';
    for ($year = $startYear; $year <= $endYear; $year++) { 
        $eraYear = convertToJapaneseEra($year);
        $selected = ($year == $selectedYear) ? 'selected' : '';  // selected属性を設定
        $options .= "<option value=\"$year\" $selected>$eraYear</option>";
    }
    return $options;
}

try {
    $pdo = new PDO($dsnVehicles, $userVehicles, $passwordVehicles);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];
    
    if (is_numeric($car_id)) {
        $car_id = intval($car_id);
    } else {
        exit('無効な車両IDです。');
    }

    $stmt = $pdo->prepare('SELECT * FROM vehicles WHERE car_id = :car_id');
    $stmt->bindValue(':car_id', $car_id, PDO::PARAM_INT);
    $stmt->execute();
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicle) {
        exit('車両データが見つかりませんでした。');
    }
    
    // データベースから取得した日付データを分割する処理
    list($first_registration_year, $first_registration_month, $first_registration_day) = splitDate($vehicle['first_registration_day'] ?? '');
    list($vehicle_inspection_year, $vehicle_inspection_month, $vehicle_inspection_day) = splitDate($vehicle['vehicle_inspection_day'] ?? '');
    list($compulsory_automobile_year, $compulsory_automobile_month, $compulsory_automobile_day) = splitDate($vehicle['compulsory_automobile_day'] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suspend'])) {
    $car_id = $_POST['car_id'];

    if (is_numeric($car_id)) {
        $car_id = intval($car_id);
    } else {
        exit('無効な車両IDです。');
    }

    $car_number_name = htmlspecialchars($_POST['car_number_name'], ENT_QUOTES, 'UTF-8');
    $sql = 'UPDATE vehicles SET is_suspended = 1 WHERE car_id = :car_id';

    $stmt_update = $pdo->prepare($sql);
    $stmt_update->bindValue(':car_id', $car_id, PDO::PARAM_INT);

    try {
        $stmt_update->execute();
        header("Location: list.php?message={$car_number_name}号車を休車しました。");
        exit();
    } catch (PDOException $e) {
        echo '更新に失敗しました: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $car_id = $_POST['car_id'];

    if (is_numeric($car_id)) {
        $car_id = intval($car_id);
    } else {
        exit('無効な車両IDです。');
    }

    $car_number01 = $_POST['car_number01'] ?? '';
    $car_number02 = $_POST['car_number02'] ?? '';
    $car_number = $car_number01 . '-' . $car_number02;
    $first_registration_year = $_POST['first_registration_year'] ?? null;
    $first_registration_month = $_POST['first_registration_month'] ?? null;
    $vehicle_inspection_year = $_POST['vehicle_inspection_year'] ?? null;
    $vehicle_inspection_month = $_POST['vehicle_inspection_month'] ?? null;
    $vehicle_inspection_day = $_POST['vehicle_inspection_day'] ?? null;

    if ($first_registration_year && $first_registration_month) {
        $first_registration_date = $first_registration_year . '-' . str_pad($first_registration_month, 2, '0', STR_PAD_LEFT) . '-01';
    }

    if ($vehicle_inspection_year && $vehicle_inspection_month && $vehicle_inspection_day) {
        $vehicle_inspection_date = $vehicle_inspection_year . '-' . str_pad($vehicle_inspection_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($vehicle_inspection_day, 2, '0', STR_PAD_LEFT);
    }

    $car_model = $_POST['car_model'] ?? null;
    $car_name = $_POST['car_name'] ?? null;
    $car_transpottaition = $_POST['car_transpottaition'] ?? null;
    $car_classification_no = $_POST['car_classification_no'] ?? null;
    $car_purpose = $_POST['car_purpose'] ?? null;
    $car_chassis_number = $_POST['car_chassis_number'] ?? null;
    $compulsory_automobile_year = $_POST['compulsory_automobile_year'] ?? null;
    $compulsory_automobile_month = $_POST['compulsory_automobile_month'] ?? null;
    $compulsory_automobile_day = $_POST['compulsory_automobile_day'] ?? null;
    $owner_name = $_POST['owner_name'] ?? null;
    $owner_address = $_POST['owner_address'] ?? null;
    $user_name = $_POST['user_name'] ?? null;
    $user_address = $_POST['user_address'] ?? null;
    $headquarters_address = $_POST['headquarters_address'] ?? null;

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    } else {
        $sql = '
        UPDATE vehicles
        SET 
            car_number = :car_number,
            car_model = :car_model,
            car_name = :car_name,
            car_transpottaition = :car_transpottaition,
            car_classification_no = :car_classification_no,
            car_purpose = :car_purpose,
            car_chassis_number = :car_chassis_number,
            first_registration_day = :first_registration_day,
            vehicle_inspection_day = :vehicle_inspection_day,
            owner_name = :owner_name,
            owner_address = :owner_address,
            user_name = :user_name,
            user_address = :user_address,
            headquarters_address = :headquarters_address,
            is_suspended = 0,
            vehicle_updateday = NOW()
        WHERE car_id = :car_id
    ';

        $stmt_update = $pdo->prepare($sql);

        $stmt_update->bindValue(':car_number', $car_number, PDO::PARAM_STR);  
        $stmt_update->bindValue(':car_model', $car_model, PDO::PARAM_STR);
        $stmt_update->bindValue(':car_name', $car_name, PDO::PARAM_STR);
        $stmt_update->bindValue(':car_transpottaition', $car_transpottaition, PDO::PARAM_STR);
        $stmt_update->bindValue(':car_classification_no', $car_classification_no, PDO::PARAM_INT);
        $stmt_update->bindValue(':car_purpose', $car_purpose, PDO::PARAM_STR);
        $stmt_update->bindValue(':car_chassis_number', $car_chassis_number, PDO::PARAM_STR);
        $stmt_update->bindValue(':first_registration_day', $first_registration_date, PDO::PARAM_STR);
        $stmt_update->bindValue(':vehicle_inspection_day', $vehicle_inspection_date, PDO::PARAM_STR);
        $stmt_update->bindValue(':owner_name', $owner_name, PDO::PARAM_STR);
        $stmt_update->bindValue(':owner_address', $owner_address, PDO::PARAM_STR);
        $stmt_update->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt_update->bindValue(':user_address', $user_address, PDO::PARAM_STR);
        $stmt_update->bindValue(':headquarters_address', $headquarters_address, PDO::PARAM_STR);
        $stmt_update->bindValue(':car_id', $car_id, PDO::PARAM_INT);

        try {
            $stmt_update->execute();
            $car_number_name = htmlspecialchars($car_number, ENT_QUOTES, 'UTF-8');
            header("Location: list.php?message={$car_number_name}号車を更新しました。");
            exit();
        } catch (PDOException $e) {
            echo '更新に失敗しました: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>車両編集</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="register.css">
</head>

<header>
    <h1>車両編集</h1>
    <!-- パンクズナビ -->
    <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="../main_menu/index.php">
                <span itemprop="name">メインメニュー</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="list.php">
                <span itemprop="name">車両一覧</span>
            </a>
            <meta itemprop="position" content="2" />
        </li>
    </ol>
</header>

<body>
    <form action="update.php" method="POST">
        <input type="hidden" name="car_id" value="<?= htmlspecialchars($vehicle['car_id'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="h-adr">
            <table class="first-table">
                <tr>
                    <th>車番<span class="required"> *</span></th>
                    <td>
                        <input type="text" name="car_number_name" value="<?= htmlspecialchars($vehicle['car_number_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                    <th>車種<span class="required"> *</span></th>
                    <td>
                        <input type="text" name="car_model" value="<?= htmlspecialchars($vehicle['car_model'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>車名<span class="required"> *</span></th>
                    <td>
                        <input type="text" name="car_name" value="<?= htmlspecialchars($vehicle['car_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                    <?php if (isset($vehicle['owner_name']) && $vehicle['owner_name'] !== '辰巳タクシー株式会社'): ?>
                        <th>リース状況</th>
                        <td>リース中</td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>車両番号<span class="required"> *</span></th>
                    <td colspan="3">
                        <input type="text" class="text small" name="car_transpottaition" value="<?= htmlspecialchars($vehicle['car_transpottaition'], ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                        <input type="text" class="text small" name="car_classification_no" value="<?= htmlspecialchars($vehicle['car_classification_no'], ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                        <input type="text" class="text small" name="car_purpose" value="<?= htmlspecialchars($vehicle['car_purpose'], ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                        <?php
                            // データベースから取得したcar_numberを分割
                            $car_number_parts = explode('-', $vehicle['car_number']);
                            $car_number01 = $car_number_parts[0] ?? '';  // 例: "29"
                            $car_number02 = $car_number_parts[1] ?? '';  // 例: "69"
                        ?>
                        <!-- 分割した値を2つの入力フィールドに表示 -->
                        <input type="text" class="text small" name="car_number01" value="<?= htmlspecialchars($car_number01, ENT_QUOTES, 'UTF-8'); ?>"> -
                        <input type="text" class="text small" name="car_number02" value="<?= htmlspecialchars($car_number02, ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>車台番号<span class="required"> *</span></th>
                    <td>
                        <input type="text" class="text" name="car_chassis_number" value="<?= htmlspecialchars($vehicle['car_chassis_number'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>初年度登録年月<span class="required"> *</span></th>
                    <td colspan="2">
                        <select name="first_registration_year">
                            <?= generateJapaneseYearOptions(1999, date("Y") + 1, $first_registration_year); ?>
                        </select>年
                        <select name="first_registration_month">
                            <?= generateMonthOptions($first_registration_month); ?>
                        </select>月
                    </td>
                </tr>
                <tr>
                    <th>車検有効期限<span class="required"> *</span></th>
                    <td colspan="3">
                        <select name="vehicle_inspection_year">
                            <?= generateJapaneseYearOptions(date("Y") - 3, date("Y") + 3, $vehicle_inspection_year); ?>
                        </select>年
                        <select name="vehicle_inspection_month">
                            <?= generateMonthOptions($vehicle_inspection_month); ?>
                        </select>月
                        <select name="vehicle_inspection_day">
                            <?= generateDayOptions($vehicle_inspection_day); ?>
                        </select>日
                    </td>
                </tr>
                <tr>
                    <th>自賠責有効期限<span class="required"> *</span></th>
                    <td colspan="3">
                        <select name="compulsory_automobile_year">
                            <?= generateJapaneseYearOptions(date("Y") - 2, date("Y") + 2, $compulsory_automobile_year); ?>
                        </select>年
                        <select name="compulsory_automobile_month">
                            <?= generateMonthOptions($compulsory_automobile_month); ?>
                        </select>月
                        <select name="compulsory_automobile_day">
                            <?= generateDayOptions($compulsory_automobile_day); ?>
                        </select>日
                    </td>
                </tr>
            </table>
        </div>

        <div class="h-adr">
            <table class="second-table">
                <tr>
                    <th colspan="2">所有者・使用者情報</th>
                </tr>
                <tr>
                    <th>所有者の氏名<br>又は名称</th>
                    <td colspan="2">
                        <input type="text" class="input large" name="owner_name" value="<?= htmlspecialchars($vehicle['owner_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>所有者の住所</th>
                    <td colspan="2">
                        <input type="text" class="input large" name="owner_address" value="<?= htmlspecialchars($vehicle['owner_address'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>使用者の氏名<br>又は名称</th>
                    <td colspan="2">
                        <input type="text" class="input large" name="user_name" value="<?= htmlspecialchars($vehicle['user_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>使用者の住所</th>
                    <td colspan="2">
                        <input type="text" class="input large" name="user_address" value="<?= htmlspecialchars($vehicle['user_address'], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <th>使用本拠地の<br>位置<span class="required"> *</span></th>
                    <td>
                        <select name="headquarters_address" class="large-select">
                            <?= generateSelectOptions(HEADQUARTERS_ADDRESS, $vehicle['headquarters_address']); ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <?php if (isset($logged_in_workclass) && ($logged_in_workclass === 1 || $logged_in_workclass === 2)): ?>
            <div class="flex">
                <input type="submit" value="更新" name="update">
                <input type="submit" value="休車" name="suspend">
            </div>
        <?php endif; ?>
    </form>
</body>
</html>