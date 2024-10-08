<?php
session_start();
// ユーザーがログインしているか確認
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
//     header("Location: ../login/index.php");
//     exit;
// }
$errors = []; 
require '../common/config.php';  // ここでPDOインスタンスが含まれるconfig.phpをインクルード
require '../common/validation_vehicle.php';  

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
    return $year . '年'; // 元号に該当しない場合は西暦を返す
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // 新規登録処理
    // validateAllVehicleData($pdoVehicles, false);  // config.phpで生成された $pdoVehicles を利用
    validateAllVehicleData($pdoAccount, false); 
    // エラーがない場合のみ処理を続ける
    if (empty($errors)) {
        // car_number01 と car_number02 を結合して car_number を作成
        $car_number01 = $_POST['car_number01'] ?? '';
        $car_number02 = $_POST['car_number02'] ?? '';
        $car_number = $car_number01 . '-' . $car_number02;

        // 初年度登録年月をDATE型に変換
        $first_registration_year = $_POST['first_registration_year'] ?? null;
        $first_registration_month = $_POST['first_registration_month'] ?? null;
        if ($first_registration_year && $first_registration_month) {
            $first_registration_date = $first_registration_year . '-' . str_pad($first_registration_month, 2, '0', STR_PAD_LEFT) . '-01';
        }

        // 車検有効期限をDATE型に変換
        $vehicle_inspection_year = $_POST['vehicle_inspection_year'] ?? null;
        $vehicle_inspection_month = $_POST['vehicle_inspection_month'] ?? null;
        $vehicle_inspection_day = $_POST['vehicle_inspection_day'] ?? null;
        if ($vehicle_inspection_year && $vehicle_inspection_month && $vehicle_inspection_day) {
            $vehicle_inspection_date = $vehicle_inspection_year . '-' . str_pad($vehicle_inspection_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($vehicle_inspection_day, 2, '0', STR_PAD_LEFT);
        }

        // 自賠責有効期限をDATE型に変換
        $compulsory_automobile_year = $_POST['compulsory_automobile_year'] ?? null;
        $compulsory_automobile_month = $_POST['compulsory_automobile_month'] ?? null;
        $compulsory_automobile_day = $_POST['compulsory_automobile_day'] ?? null;
        if ($compulsory_automobile_year && $compulsory_automobile_month && $compulsory_automobile_day) {
            $compulsory_automobile_date = $compulsory_automobile_year . '-' . str_pad($compulsory_automobile_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($compulsory_automobile_day, 2, '0', STR_PAD_LEFT);
        }

        // メーター検査有効期限をDATE型に変換
        $meter_inspection_year = $_POST['meter_inspection_year'] ?? null;
        $meter_inspection_month = $_POST['meter_inspection_month'] ?? null;
        if ($meter_inspection_year && $meter_inspection_month) {
            $meter_inspection_date = $meter_inspection_year . '-' . str_pad($meter_inspection_month, 2, '0', STR_PAD_LEFT) . '-01';
        } else {
            $meter_inspection_date = null;
        }

        // LPガス容器有効期限をDATE型に変換
        $lp_gas_year = $_POST['lp_gas_year'] ?? null;
        $lp_gas_month = $_POST['lp_gas_month'] ?? null;
        if ($lp_gas_year && $lp_gas_month) {
            $lp_gas_date = $lp_gas_year . '-' . str_pad($lp_gas_month, 2, '0', STR_PAD_LEFT) . '-01';
        } else {
            $lp_gas_date = null;
        }

        try {
            // データベースへの挿入処理
            $sql = '
                INSERT INTO vehicles (
                    car_number_name, car_number, car_model, car_name, car_transpottaition, car_classification_no,
                    car_purpose, car_chassis_number, first_registration_day,
                    vehicle_inspection_day, compulsory_automobile_day, meter_inspection_day, lp_gas_day,
                    owner_name, owner_address, user_name, user_address, headquarters_address, vehicle_registrationday
                ) VALUES (
                    :car_number_name, :car_number, :car_model, :car_name, :car_transpottaition, :car_classification_no,
                    :car_purpose, :car_chassis_number, :first_registration_date,
                    :vehicle_inspection_date, :compulsory_automobile_date, :meter_inspection_day, :lp_gas_day,
                    :owner_name, :owner_address, :user_name, :user_address, :headquarters_address, NOW()
                )';

            // $stmt = $pdoVehicles->prepare($sql);  // $pdoVehicles を使用
            $stmt = $pdoAccount->prepare($sql);

            // プレースホルダーに値をバインド
            $stmt->bindValue(':car_number_name', $_POST['car_number_name'], PDO::PARAM_STR);
            $stmt->bindValue(':car_number', $car_number, PDO::PARAM_STR);
            $stmt->bindValue(':car_model', $_POST['car_model'], PDO::PARAM_STR);
            $stmt->bindValue(':car_name', $_POST['car_name'], PDO::PARAM_STR);
            $stmt->bindValue(':car_transpottaition', $_POST['car_transpottaition'], PDO::PARAM_STR);
            $stmt->bindValue(':car_classification_no', $_POST['car_classification_no'], PDO::PARAM_INT);
            $stmt->bindValue(':car_purpose', $_POST['car_purpose'], PDO::PARAM_STR);
            $stmt->bindValue(':car_chassis_number', $_POST['car_chassis_number'], PDO::PARAM_STR);
            $stmt->bindValue(':first_registration_date', $first_registration_date, PDO::PARAM_STR);
            $stmt->bindValue(':vehicle_inspection_date', $vehicle_inspection_date, PDO::PARAM_STR);
            $stmt->bindValue(':compulsory_automobile_date', $compulsory_automobile_date, PDO::PARAM_STR);
            $stmt->bindValue(':meter_inspection_day', $meter_inspection_date, PDO::PARAM_STR); 
            $stmt->bindValue(':lp_gas_day', $lp_gas_date, PDO::PARAM_STR); 
            $stmt->bindValue(':owner_name', $_POST['owner_name'], PDO::PARAM_STR);
            $stmt->bindValue(':owner_address', $_POST['owner_address'], PDO::PARAM_STR);
            $stmt->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
            $stmt->bindValue(':user_address', $_POST['user_address'], PDO::PARAM_STR);
            $stmt->bindValue(':headquarters_address', $_POST['headquarters_address'], PDO::PARAM_STR);

            // SQL実行
            $stmt->execute();

            // 成功メッセージを表示してリダイレクト
            $car_number_name = htmlspecialchars($_POST['car_number_name'], ENT_QUOTES, 'UTF-8');
            $message = urlencode($car_number_name . "号車を登録しました。");
            header("Location: list.php?message=$message");
            exit();  // スクリプトの終了

        } catch (PDOException $e) {
            // データベースエラーが発生した場合、エラーメッセージを表示
            echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>車両新規登録</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
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
                            <input type="text" class="text small" size="3" maxlength="3" placeholder="500" name="car_classification_no" value="<?= htmlspecialchars($_POST['car_classification_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                            <input type="text" class="text small" size="1" maxlength="1" placeholder="あ" name="car_purpose" value="<?= htmlspecialchars($_POST['car_purpose'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">&nbsp;
                            <input type="text" class="text small" size="2" maxlength="2" placeholder="29" name="car_number01" value="<?= htmlspecialchars($_POST['car_number01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <input type="text" class="text small" size="2" maxlength="2" placeholder="29" name="car_number02" value="<?= htmlspecialchars($_POST['car_number02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            
                            <?php if (isset($errors['vehicle_number'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['vehicle_number'], ENT_QUOTES, 'UTF-8'); ?></span>
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
                                    $currentYear = date("Y");  
                                    $startYear = $currentYear - 3;
                                    $endYear = $currentYear + 1;
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
                                    $currentYear = date("Y");
                                    $startYear = $currentYear - 1;
                                    $endYear = $currentYear + 1;
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
                    <tr>
                        <th>メーター検査有効期限</th>
                        <td colspan="2">
                            <select name="meter_inspection_year">
                                <option value="">未選択</option>
                                <?php
                                    $currentYear = date("Y");
                                    $startYear = $currentYear - 1;
                                    $endYear = $currentYear + 1;
                                    echo generateJapaneseYearOptions($startYear, $endYear, $_POST['meter_inspection_year'] ?? '');
                                ?>
                            </select>年
                            <select name="meter_inspection_month">
                                <option value="">未選択</option>
                                <?= generateMonthOptions($_POST['meter_inspection_month'] ?? '') ?>
                            </select>月
                        </td>
                    </tr>
                    <tr>
                        <th>LPガス容器有効期限</th>
                        <td colspan="2">
                            <select name="lp_gas_year">
                                <option value="">未選択</option>
                                <?php
                                    $currentYear = date("Y");
                                    $startYear = $currentYear - 1;
                                    $endYear = $currentYear + 10;
                                    echo generateJapaneseYearOptions($startYear, $endYear, $_POST['lp_gas_year'] ?? '');
                                ?>
                            </select>年
                            <select name="lp_gas_month">
                                <option value="">未選択</option>
                                <?= generateMonthOptions($_POST['lp_gas_month'] ?? '') ?>
                            </select>月
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
                        <th>所有者の氏名<br>又は名称<span class="required"> *</span></th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="辰巳タクシー株式会社" name="owner_name" value="<?= htmlspecialchars($_POST['owner_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['owner_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['owner_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>所有者の住所<span class="required"> *</span></th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="静岡県静岡市葵区駒形通2丁目2-25" name="owner_address" value="<?= htmlspecialchars($_POST['owner_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['owner_address'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['owner_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用者の氏名<br>又は名称<span class="required"> *</span></th>
                        <td colspan="2">
                            <input type="text" class="input large" placeholder="辰巳タクシー株式会社" name="user_name" value="<?= htmlspecialchars($_POST['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['user_name'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['user_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用者の住所<span class="required"> *</span></th>
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
                <input type="submit" value="登録" name="create">
            </div>
            
        </form>

    </body>

</html>