<?php
session_start();
$errors = []; 
require '../config/config.php'; 
require '../config/validation.php';  

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;

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

function generateJapaneseYearOptions($startYear, $endYear, $selectedYear) {
    $options = '';
    for ($year = $startYear; $year <= $endYear; $year++) { 
        $eraYear = convertToJapaneseEra($year);
        $selected = ($year == $selectedYear) ? 'selected' : '';
        $options .= "<option value=\"$year\" $selected>$eraYear</option>";
    }
    return $options;
}

// ナンバーのフォーム送信後の値を取得
$car_number01 = $_POST['car_number01'] ?? ''; 
$car_number02 = $_POST['car_number02'] ?? ''; 

// car_number01の表示処理
if (strlen($car_number01) === 1) {
    $car_number01 = '･' . $car_number01; // car_number01が1桁の場合に「･1」と表示
} elseif (empty($car_number01)) {
    $car_number01 = '･･'; // car_number01が空白の場合に「･･」と表示
}

// car_number02の表示処理
if (strlen($car_number02) === 1) {
    $car_number02 = '･' . $car_number02; // car_number02が1桁の場合に「･3」と表示
}


try {
    $pdo = new PDO($dsnVehicles, $userVehicles, $passwordVehicles);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // データベースへ挿入処理
        $sql = '
            INSERT INTO vehicless (
                car_number_name, car_model, car_name, car_transpottaition, car_classification_no,
                car_purpose, car_number01, car_number02, car_chassis_number, first_registration_year,
                first_registration_month, vehicle_inspection_year, vehicle_inspection_month,
                vehicle_inspection_day, compulsory_automobile_year, compulsory_automobile_month,
                compulsory_automobile_day, owner_name, owner_address, user_name, user_address,
                headquarters_address, vehicle_registrationday
            ) VALUES (
                :car_number_name, :car_model, :car_name, :car_transpottaition, :car_classification_no,
                :car_purpose, :car_number01, :car_number02, :car_chassis_number, :first_registration_year,
                :first_registration_month, :vehicle_inspection_year, :vehicle_inspection_month,
                :vehicle_inspection_day, :compulsory_automobile_year, :compulsory_automobile_month,
                :compulsory_automobile_day, :owner_name, :owner_address, :user_name, :user_address,
                :headquarters_address, NOW()
            )';

        $stmt = $pdo->prepare($sql);

        // 値をバインド
        $stmt->bindValue(':car_number_name', $_POST['car_number_name'], PDO::PARAM_INT);
        $stmt->bindValue(':car_model', $_POST['car_model'], PDO::PARAM_STR);
        $stmt->bindValue(':car_name', $_POST['car_name'], PDO::PARAM_STR);
        $stmt->bindValue(':car_transpottaition', $_POST['car_transpottaition'], PDO::PARAM_STR);
        $stmt->bindValue(':car_classification_no', $_POST['car_classification_no'], PDO::PARAM_INT);
        $stmt->bindValue(':car_purpose', $_POST['car_purpose'], PDO::PARAM_STR);
        $stmt->bindValue(':car_number01', $car_number01, PDO::PARAM_STR);  // 修正した値をバインド
        $stmt->bindValue(':car_number02', $car_number02, PDO::PARAM_STR);
        $stmt->bindValue(':car_chassis_number', $_POST['car_chassis_number'], PDO::PARAM_STR);
        $stmt->bindValue(':first_registration_year', $_POST['first_registration_year'], PDO::PARAM_INT);
        $stmt->bindValue(':first_registration_month', $_POST['first_registration_month'], PDO::PARAM_INT);
        $stmt->bindValue(':vehicle_inspection_year', $_POST['vehicle_inspection_year'], PDO::PARAM_INT);
        $stmt->bindValue(':vehicle_inspection_month', $_POST['vehicle_inspection_month'], PDO::PARAM_INT);
        $stmt->bindValue(':vehicle_inspection_day', $_POST['vehicle_inspection_day'], PDO::PARAM_INT);
        $stmt->bindValue(':compulsory_automobile_year', $_POST['compulsory_automobile_year'], PDO::PARAM_INT);
        $stmt->bindValue(':compulsory_automobile_month', $_POST['compulsory_automobile_month'], PDO::PARAM_INT);
        $stmt->bindValue(':compulsory_automobile_day', $_POST['compulsory_automobile_day'], PDO::PARAM_INT);
        $stmt->bindValue(':owner_name', $_POST['owner_name'], PDO::PARAM_STR);
        $stmt->bindValue(':owner_address', $_POST['owner_address'], PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
        $stmt->bindValue(':user_address', $_POST['user_address'], PDO::PARAM_STR);
        $stmt->bindValue(':headquarters_address', $_POST['headquarters_address'], PDO::PARAM_INT);

        $stmt->execute();

        $car_number_name = htmlspecialchars($_POST['car_number_name'], ENT_QUOTES, 'UTF-8');
    $message = urlencode($car_number_name . "号車を登録しました。");
    
    // リダイレクト処理
    header("Location: list.php?message=$message");
    exit();  // スクリプトの終了

} catch (PDOException $e) {
    echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
}

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>車両新規登録</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
        <!-- <script src="https://yubinbango.github.io/yubinbango/yubinbango.js"></script> -->
    </head>
    <body>
        <header>
            <h1>車両新規登録</h1>
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

        <form action="register.php" method="POST">
            <div class="h-adr">
                <!-- <span class="p-country-name" style="display:none;">Japan</span> -->
                <table class="first-table">
                    <tr>
                        <th>車番<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="car_number_name" placeholder="121" value="<?= htmlspecialchars($_POST['car_number_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['car_number_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_number_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>車種<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="car_model" placeholder="コンフォート" value="<?= htmlspecialchars($_POST['car_model'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['car_model'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_model'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>車名<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="car_name" placeholder="トヨタ" value="<?= htmlspecialchars($_POST['car_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['car_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>車両番号<span class="required"> *</span></th>
                        <td colspan="3">
                            <input type="text" class="text small" placeholder="静岡" name="car_transpottaition" value="<?= htmlspecialchars($_POST['car_transpottaition'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                            <?php if (isset($errors['car_transpottaition'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_transpottaition'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <input type="text" class="text small" size="3" maxlength="3" placeholder="500" name="car_classification_no" value="<?= htmlspecialchars($_POST['car_classification_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                            <?php if (isset($errors['car_classification_no'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_classification_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <input type="text" class="text small" size="1" maxlength="1" placeholder="あ" name="car_purpose" value="<?= htmlspecialchars($_POST['car_purpose'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                            <?php if (isset($errors['car_purpose'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_purpose'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <input type="text" class="text small" size="2" maxlength="2" placeholder="29" name="car_number01" value="<?= htmlspecialchars($_POST['car_number01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                                <?php if (isset($errors['car_number01'])): ?>
                                    <br><span class="error"><?php echo htmlspecialchars($errors['car_number01'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            <input type="text" class="text small" size="2" maxlength="2" placeholder="29" name="car_number02" value="<?= htmlspecialchars($_POST['car_number02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['car_number02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_number02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>車台番号<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="TSS11-9012867" name="car_chassis_number" value="<?= htmlspecialchars($_POST['car_chassis_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['car_chassis_number'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['car_chassis_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>初年度登録年月<span class="required"> *</span></th>
                        <td colspan="2">
                            <select name="first_registration_year">
                                <?php
                                    $startYear = 1999;
                                    $endYear = date("Y") + 1;  
                                    echo "Debug: startYear = $startYear, endYear = $endYear"; // デバッグ用
                                    echo generateJapaneseYearOptions($startYear, $endYear, $_POST['first_registration_year'] ?? '');
                                ?>
                            </select>年
                            <select name="first_registration_month">
                                <?= generateMonthOptions($_POST['first_registration_month'] ?? '') ?>
                            </select>月
                            <?php if (isset($errors['first_registration_date'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['first_registration_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>車検有効期限<span class="required"> *</span></th>
                        <td colspan="3">
                            <select name="vehicle_inspection_year">
                                <?php
                                    $currentYear = date("Y");  // 現在の年を取得
                                    $startYear = $currentYear - 3;  // 現在の年から3年前をスタート年に設定
                                    $endYear = $currentYear + 1;   // 現在の年から1年後を終了年に設定
                                    echo generateJapaneseYearOptions($startYear, $endYear, $_POST['vehicle_inspection_year'] ?? '');
                                ?>
                            </select>年
                            <select name="vehicle_inspection_month">
                                <?= generateMonthOptions($_POST['vehicle_inspection_month'] ?? '') ?>
                            </select>月
                            <select name="vehicle_inspection_day">
                                <?= generateDayOptions($_POST['vehicle_inspection_day'] ?? '') ?>
                            </select>日
                            <?php if (isset($errors['vehicle_inspection_date'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['vehicle_inspection_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>自賠責有効期限<span class="required"> *</span></th>
                        <td colspan="3">
                            <select name="compulsory_automobile_year">
                                <?php
                                    $currentYear = date("Y");  // 現在の年を取得
                                    $startYear = $currentYear - 1;  // 現在の年から1年前をスタート年に設定
                                    $endYear = $currentYear + 1;   // 現在の年から1年後を終了年に設定
                                    echo generateJapaneseYearOptions($startYear, $endYear, $_POST['compulsory_automobile_year'] ?? '');
                                ?>
                            </select>年
                            <select name="compulsory_automobile_month">
                                <?= generateMonthOptions($_POST['compulsory_automobile_month'] ?? '') ?>
                            </select>月
                            <select name="compulsory_automobile_day">
                                <?= generateDayOptions($_POST['compulsory_automobile_day'] ?? '') ?>
                            </select>日
                            <?php if (isset($errors['compulsory_automobile_date'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['compulsory_automobile_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
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
                            <input type="text" class="input large" placeholder="辰巳タクシー株式会社" name="owner_name" value="<?= htmlspecialchars($_POST['owner_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['owner_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['owner_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>所有者の住所</th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="静岡県静岡市葵区駒形通2丁目2-25" name="owner_address" value="<?= htmlspecialchars($_POST['owner_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['owner_address'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['owner_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用者の氏名<br>又は名称</th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="辰巳タクシー株式会社" name="user_name" value="<?= htmlspecialchars($_POST['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['user_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['user_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用者の住所</th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="静岡県静岡市葵区駒形通2丁目2-25" name="user_address" value="<?= htmlspecialchars($_POST['user_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['user_address'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['user_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用本拠地の<br>位置<span class="required"> *</span></th>
                        <td>
                            <select name="headquarters_address" class="large-select">
                                <?= generateSelectOptions(HEADQUARTERS_ADDRESS); ?>
                            </select>
                            <?php if (!empty($errors['headquarters_address'])): ?>
                                <span class="error"><?= htmlspecialchars($errors['headquarters_address']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>    
            </div>

            <div class="flex">
                <input type="submit" value="登録" name="submit">
            </div>
        </form>
    </body>
</html>