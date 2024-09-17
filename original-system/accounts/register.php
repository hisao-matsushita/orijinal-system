<?php
session_start();
$errors = [];
require '../common/config.php';  // config.phpで作成したPDOインスタンスを使用
require '../common/validation_account.php';  // バリデーション用のファイルを読み込み

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

// POSTリクエストが送信された場合のみ処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // バリデーションを実行
    performValidation($errors, $pdoAccount, $patterns);

    // エラーがなければデータベースにデータを挿入
    if (empty($errors)) {
        // 日付や電話番号などの結合処理
        $account_birthday = $_POST['account_birthday_year'] . '-' . str_pad($_POST['account_birthday_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['account_birthday_day'], 2, '0', STR_PAD_LEFT);
        $account_license_expiration_date = $_POST['account_license_expiration_date_year'] . '-' . str_pad($_POST['account_license_expiration_date_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['account_license_expiration_date_day'], 2, '0', STR_PAD_LEFT);
        $account_employment_date = $_POST['account_employment_year'] . '-' . str_pad($_POST['account_employment_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['account_employment_day'], 2, '0', STR_PAD_LEFT);
        $account_appointment_date = $_POST['account_appointment_year'] . '-' . str_pad($_POST['account_appointment_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['account_appointment_day'], 2, '0', STR_PAD_LEFT);
        $account_retirement_date = $_POST['account_retirement_year'] . '-' . str_pad($_POST['account_retirement_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['account_retirement_day'], 2, '0', STR_PAD_LEFT);
        $account_zipcord = $_POST['account_zipcord01'] . '-' . $_POST['account_zipcord02'];
        $account_tel1 = $_POST['account_tel01'] . '-' . $_POST['account_tel02'] . '-' . $_POST['account_tel03'];
        $account_tel2 = $_POST['account_tel04'] . '-' . $_POST['account_tel05'] . '-' . $_POST['account_tel06'];
        $account_guarentor_zipcode = $_POST['account_guarentor_zipcord01'] . '-' . $_POST['account_guarentor_zipcord02'];
        $account_guarentor_tel1 = $_POST['account_guarentor_tel01'] . '-' . $_POST['account_guarentor_tel02'] . '-' . $_POST['account_guarentor_tel03'];
        $account_guarentor_tel2 = $_POST['account_guarentor_tel04'] . '-' . $_POST['account_guarentor_tel05'] . '-' . $_POST['account_guarentor_tel06'];

        try {
            // SQLクエリ
            $sql = '
                INSERT INTO accounts (
                    account_no, account_salesoffice, account_kana01, account_kana02, account_name01, account_name02,
                    account_birthday, account_jenda, account_bloodtype, 
                    account_zipcord, account_pref, account_address01, account_address02, account_address03,
                    account_tel1, account_tel2, account_license_expiration_date,
                    account_guarentor_kana01, account_guarentor_kana02, account_guarentor_name01, account_guarentor_name02, account_relationship,
                    account_guarentor_zipcode, account_guarentor_pref, account_guarentor_address01, account_guarentor_address02, account_guarentor_address03,
                    account_guarentor_tel1, account_guarentor_tel2,
                    account_department, account_workclass, account_classification, account_enrollment,  
                    account_employment_date, account_appointment_date, account_retirement_date, registration_date
                )
                VALUES (
                    :account_no, :account_salesoffice, :account_kana01, :account_kana02, :account_name01, :account_name02,
                    :account_birthday, :account_jenda, :account_bloodtype,       
                    :account_zipcord, :account_pref, :account_address01, :account_address02, :account_address03,
                    :account_tel1, :account_tel2, :account_license_expiration_date,
                    :account_guarentor_kana01, :account_guarentor_kana02, :account_guarentor_name01, :account_guarentor_name02, :account_relationship,
                    :account_guarentor_zipcode, :account_guarentor_pref, :account_guarentor_address01, :account_guarentor_address02, :account_guarentor_address03,
                    :account_guarentor_tel1, :account_guarentor_tel2,
                    :account_department, :account_workclass, :account_classification, :account_enrollment,  
                    :account_employment_date, :account_appointment_date, :account_retirement_date, NOW()
                )';

            $stmt = $pdoAccount->prepare($sql);

            // 値をバインド
            $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_INT);
            $stmt->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_INT);
            $stmt->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_birthday', $account_birthday, PDO::PARAM_STR);
            $stmt->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_INT);
            $stmt->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_INT);
            $stmt->bindValue(':account_zipcord', $account_zipcord, PDO::PARAM_STR);
            $stmt->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR);
            $stmt->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR);
            $stmt->bindValue(':account_tel1', $account_tel1, PDO::PARAM_STR);  
            $stmt->bindValue(':account_tel2', $account_tel2, PDO::PARAM_STR);
            $stmt->bindValue(':account_license_expiration_date', $account_license_expiration_date, PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_zipcode', $account_guarentor_zipcode, PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_tel1', $account_guarentor_tel1, PDO::PARAM_STR);
            $stmt->bindValue(':account_guarentor_tel2', $account_guarentor_tel2, PDO::PARAM_STR);
            $stmt->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_INT);
            $stmt->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_INT);
            $stmt->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_INT);
            $stmt->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_INT);
            $stmt->bindValue(':account_employment_date', $account_employment_date, PDO::PARAM_STR);
            $stmt->bindValue(':account_appointment_date', $account_appointment_date, PDO::PARAM_STR);
            $stmt->bindValue(':account_retirement_date', $account_retirement_date, PDO::PARAM_STR);

            // SQLの実行
            $stmt->execute();

            // 登録完了後、一覧ページにリダイレクト
            $message = urlencode("従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に登録されました。");
            header("Location: list.php?message=$message");
            exit();

        } catch (PDOException $e) {
            echo 'データベースエラー: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>従業員新規登録</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
        <script src="https://yubinbango.github.io/yubinbango/yubinbango.js"></script>
    </head>

    <header>
        <h1>従業員新規登録</h1>
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
                    <span itemprop="name">従業員一覧</span>
                </a>
                <meta itemprop="position" content="2" />
            </li>
        </ol>
    </header>

    <body>
        <form action="" method="POST">
            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="first-table">
                    <tr>
                        <th>パスワード</th>
                        <td>
                            <input type="password" name="account_password" placeholder="t0542542471" value="<?= htmlspecialchars($_POST['account_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_password'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_password'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>  
                    </tr>
                    <tr>
                        <th>従業員No<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="account_no" placeholder="1111" value="<?= htmlspecialchars($_POST['account_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_no'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>所属営業所<span class="required"> *</span></th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE); ?>
                            </select>
                            <?php if (isset($errors['account_salesoffice'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_salesoffice'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" value="<?= htmlspecialchars($_POST['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" value="<?= htmlspecialchars($_POST['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（漢字）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_name01" value="<?= htmlspecialchars($_POST['account_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_name01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_name01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（漢字）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="一番" name="account_name02" value="<?= htmlspecialchars($_POST['account_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_name02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_name02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                        <th>生年月日<span class="required"> *</span></th>
                        <td colspan="2">
                            <select name="account_birthday_year">
                                <?php
                                    $startYear = 1939;
                                    $endYear = date("Y") - 25;  // 現在の年から25年前を終了年に設定
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_birthday_year'] ?? '');
                                ?>
                            </select>年
                            <select name="account_birthday_month">
                                <?= generateMonthOptions($_POST['account_birthday_month'] ?? '') ?>
                            </select>月  
                            <select name="account_birthday_day">
                                <?= generateDayOptions($_POST['account_birthday_day'] ?? '') ?>
                            </select>日
                            <?php if (isset($errors['account_birthday'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_birthday'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>性別<span class="required"> *</span></th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA); ?>
                            </select>
                            <?php if (isset($errors['account_jenda'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_jenda'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>血液型<span class="required"> *</span></th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE); ?>
                            </select>
                            <?php if (isset($errors['account_bloodtype'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_bloodtype'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号<span class="required"> *</span></th>
                        <td>
                        <?php
                                // フォームの送信データから郵便番号を取得し、ゼロパディングを適用
                        $zipcord01 = isset($_POST['account_zipcord01']) ? str_pad($_POST['account_zipcord01'], 3, '0', STR_PAD_LEFT) : '';
                        $zipcord02 = isset($_POST['account_zipcord02']) ? str_pad($_POST['account_zipcord02'], 4, '0', STR_PAD_LEFT) : '';
                            ?>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="420" value="<?= htmlspecialchars($zipcord01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="0042" value="<?= htmlspecialchars($zipcord02, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="p-region" name="account_pref" value="<?= htmlspecialchars($_POST['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_pref'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_pref'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>市町村区<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="p-locality" name="account_address01" value="<?= htmlspecialchars($_POST['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_address01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>町名番地<span class="required"> *</span></th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_address02" placeholder="駒形通2丁目2-25" value="<?= htmlspecialchars($_POST['account_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_address03" placeholder="辰巳マンション1111号室" value="<?= htmlspecialchars($_POST['account_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address03'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                    <th>連絡先1<span class="required"> *</span></th>
                        <td colspan="2">
                            <?php
                                $tel01 = isset($_POST['account_tel01']) ? $_POST['account_tel01'] : '';
                                $tel02 = isset($_POST['account_tel02']) ? $_POST['account_tel02'] : '';
                                $tel03 = isset($_POST['account_tel03']) ? $_POST['account_tel03'] : '';
                            ?>
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="0120" value="<?= htmlspecialchars($tel01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" value="<?= htmlspecialchars($tel02, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" value="<?= htmlspecialchars($tel03, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel04" size="4" maxlength="4" name="account_tel04" placeholder="0120" value="<?= htmlspecialchars($_POST['account_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel05" size="4" maxlength="4" name="account_tel05" placeholder="1234" value="<?= htmlspecialchars($_POST['account_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel06" size="4" maxlength="4" name="account_tel06" placeholder="5678" value="<?= htmlspecialchars($_POST['account_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel2'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限<span class="required"> *</span></th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $currentYear = 2024;
                                    $endYear = $currentYear + 10;
                                    echo generateYearOptions($currentYear, $endYear, $_POST['account_license_expiration_date_year'] ?? '');
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?= generateMonthOptions($_POST['account_license_expiration_date_month'] ?? '') ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?= generateDayOptions($_POST['account_license_expiration_date_day'] ?? '') ?>
                            </select>日
                            <?php if (isset($errors['account_license_expiration_date'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_license_expiration_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr> 
                </table>
            </div>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="second-table">
                    <tr>
                        <th>身元保証人<br>氏（ふりがな）</th>
                        <td>
                            <input type class="text" placeholder="たつみ" name="account_guarentor_kana01" value="<?= htmlspecialchars($_POST['account_guarentor_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>身元保証人<br>名（ふりがな）</th>
                        <td>
                            <input type class="text" placeholder="おやじ" name="account_guarentor_kana02" value="<?= htmlspecialchars($_POST['account_guarentor_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>身元保証人<br>氏（漢字）</th>
                        <td><input type class="text" placeholder="辰巳" name="account_guarentor_name01"></td>
                        <th>身元保証人<br>名（漢字）</th>
                        <td><input type class="text" placeholder="親父" name="account_guarentor_name02"></td>
                    </tr>
                    <tr>
                        <th>続柄</th>
                        <td><input type class="text" placeholder="父" name="account_relationship"></td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_guarentor_zipcord01" placeholder="420" value="<?= htmlspecialchars($_POST['account_guarentor_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_guarentor_zipcord02" placeholder="0042" value="<?= htmlspecialchars($_POST['account_guarentor_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['account_guarentor_zipcord'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_guarentor_pref" value="<?= htmlspecialchars($_POST['account_guarentor_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_guarentor_address01" value="<?= htmlspecialchars($_POST['account_guarentor_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_guarentor_address02" placeholder="駒形通2丁目2-25" value="<?= htmlspecialchars($_POST['account_guarentor_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_guarentor_address03" placeholder="辰巳マンション1111号室" value="<?= htmlspecialchars($_POST['account_guarentor_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address03'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_guarentor_tel01" size="4" maxlength="4" name="account_guarentor_tel01" placeholder="0120" value="<?= htmlspecialchars($_POST['account_guarentor_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel02" size="4" maxlength="4" name="account_guarentor_tel02" placeholder="1234" value="<?= htmlspecialchars($_POST['account_guarentor_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel03" size="4" maxlength="4" name="account_guarentor_tel03" placeholder="5678" value="<?= htmlspecialchars($_POST['account_guarentor_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>           
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel04" value="<?= htmlspecialchars($_POST['account_guarentor_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel05" value="<?= htmlspecialchars($_POST['account_guarentor_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel06" value="<?= htmlspecialchars($_POST['account_guarentor_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel2'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_guarentor_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="third-table">
                <tr>
                    <th>所属課<span class="required"> *</span></th>
                    <td>
                        <select name="account_department">
                            <?= generateSelectOptions(ACCOUNT_DEPARTMENT); ?>
                        </select>
                        <?php if (!empty($errors['account_department'])): ?>
                            <span class="error"><?= htmlspecialchars($errors['account_department']); ?></span>
                        <?php endif; ?>
                    </td>
                    <th>勤務区分<span class="required"> *</span></th>
                    <td>
                        <select name="account_workclass">
                            <?= generateSelectOptions(ACCOUNT_WORKCLASS); ?>
                        </select>
                        <?php if (!empty($errors['account_workclass'])): ?>
                            <span class="error"><?= htmlspecialchars($errors['account_workclass']); ?></span>
                        <?php endif; ?>
                    </td>
                <tr>
                    <th>職種区分<span class="required"> *</span></th>
                    <td>
                        <select name="account_classification">
                            <?= generateSelectOptions(ACCOUNT_CLASSIFICATION); ?>
                        </select>
                        <?php if (!empty($errors['account_classification'])): ?>
                            <span class="error"><?= htmlspecialchars($errors['account_classification']); ?></span>
                        <?php endif; ?>
                    </td>
                    <th>在籍区分</th>
                    <td><select name="account_enrollment">
                        <?= generateSelectOptions(ACCOUNT_ENROLLMENT); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>雇用年月日</th>
                    <td colspan="2">
                        <select name="account_employment_year">
                            <?php
                                $startYear = 1985;  // 開始年
                                $endYear = date('Y') + 1;  // 終了年を現在の年から1年後に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_employment_year'] ?? date('Y'));
                            ?>
                        </select>年
                        <select name="account_employment_month">
                            <?php
                                echo generateMonthOptions($_POST['account_employment_month'] ?? date('n'));
                            ?>
                        </select>月
                        <select name="account_employment_day">
                            <?php
                                echo generateDayOptions($_POST['account_employment_day'] ?? date('j'));
                            ?>
                        </select>日
                    </td>
                </tr>
                <tr>
                    <th>選任年月日</th>
                    <td colspan="2">
                        <select name="account_appointment_year">
                            <?php
                                $startYear = 1985;  // 開始年
                                $endYear = date('Y') + 1;  // 現在の年から1年後までを終了年に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_appointment_year'] ?? date('Y'));
                            ?>
                        </select>年
                        <select name="account_appointment_month">
                            <?php
                                echo generateMonthOptions($_POST['account_appointment_month'] ?? date('n'));
                            ?>
                        </select>月
                        <select name="account_appointment_day">
                            <?php
                                echo generateDayOptions($_POST['account_appointment_day'] ?? date('j'));
                            ?>
                        </select>日 
                    </td>
                </tr>
                <tr>
                    <th>退職年月日</th>
                    <td colspan="2">
                        <select name="account_retirement_year">
                            <?php
                                $startYear = 2020;  // 開始年
                                $endYear = date('Y') + 1;  // 現在の年から1年後を終了年に設定(自動的に1年追加される)
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_retirement_year'] ?? '');
                            ?>
                        </select>年
                        <select name="account_retirement_month">
                            <?php
                                echo generateMonthOptions($_POST['account_retirement_month'] ?? '');
                            ?>
                        </select>月
                        <select name="account_retirement_day">
                            <?php
                                echo generateDayOptions($_POST['account_retirement_day'] ?? '');
                            ?>
                        </select>日
                    </td>
                </tr> 
                <tr>
                    <th>?????</th>
                    <td><select name="account_enrollment1">
                            <option value="">選択</option>
                            <option value="1">年末調整対象</option> 
                            <option value="2">年末調整対象外</option> 
                            <option value="3">死亡退職</option> 
                        </select>
                    </td>
                </tr>                           
            </table>
            <!-- <table table class="fourth-table">
                <hr>
                <tr>
                <th>無効フラグ</th>
                <td><select name="account_invalidflag">
                        <option value="">選択</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </td>
                </tr>
            </table> -->
            <div table class="flex">
                <input type="submit" value="登録" name="submit">
            </div>
        </form>

    </body>
    
</html>