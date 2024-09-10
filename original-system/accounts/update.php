<?php
session_start();
$errors = [];
$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;
require '../config/config.php';  // config.php で作成したPDOインスタンスを利用

// ログインチェック
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../login/index.php');
    exit();
}

// データベース接続と従業員データの取得
try {
    // アカウントIDが指定されている場合、そのアカウント情報を取得
    if (isset($_GET['account_id'])) {
        $sql = 'SELECT * FROM accounts WHERE account_id = :account_id';
        $stmt = $pdoAccount->prepare($sql);  // config.phpで作成した$pdoAccountを使用
        $stmt->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$account) {
            exit('指定されたアカウントが見つかりません。');
        }

        // 既存のパスワードを保持しておく
        $existing_password = $account['account_password'];
    }

    // 日付などのデータを分割して変数に格納する関数
    function splitDate($date) {
        return $date ? explode('-', $date) : ['', '', ''];
    }

    // フォームのデータから分割処理を行う
    list($birthday_year, $birthday_month, $birthday_day) = splitDate($account['account_birthday'] ?? '');
    list($zipcode01, $zipcode02) = explode('-', $account['account_zipcord'] ?? '-');
    list($tel01, $tel02, $tel03) = explode('-', $account['account_tel1'] ?? '--');
    list($tel04, $tel05, $tel06) = explode('-', $account['account_tel2'] ?? '--');
    list($license_expiration_year, $license_expiration_month, $license_expiration_day) = splitDate($account['account_license_expiration_date'] ?? '');
    list($employment_year, $employment_month, $employment_day) = splitDate($account['account_employment_date'] ?? '');
    list($appointment_year, $appointment_month, $appointment_day) = splitDate($account['account_appointment_date'] ?? '');
    list($retirement_year, $retirement_month, $retirement_day) = splitDate($account['account_retirement_date'] ?? '');
    list($guarentor_zipcode01, $guarentor_zipcode02) = explode('-', $account['account_guarentor_zipcode'] ?? '-');
    list($guarentor_tel01, $guarentor_tel02, $guarentor_tel03) = explode('-', $account['account_guarentor_tel1'] ?? '--');
    list($guarentor_tel04, $guarentor_tel05, $guarentor_tel06) = explode('-', $account['account_guarentor_tel2'] ?? '--');

    // フォームが送信された時の処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // バリデーション関数
        function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
            if (!empty($input) && !preg_match($pattern, $input)) {
                $errors[$fieldName] = $errorMessage;
            }
        }

        // バリデーションパターン
        $patterns = [
            'half_width_numeric' => '/^\d+$/',
            'hiragana' => '/^[ぁ-んー　]+$/u',
            'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
        ];

        // 必須項目のバリデーション
        if (empty($_POST['account_no'])) $errors['account_no'] = '従業員Noは必須です。';
        if (empty($_POST['account_salesoffice'])) $errors['account_salesoffice'] = '所属営業所は必須です。';
        if (empty($_POST['account_kana01'])) $errors['account_kana01'] = '氏（ふりがな）は必須です。';
        if (empty($_POST['account_kana02'])) $errors['account_kana02'] = '名（ふりがな）は必須です。';
        if (empty($_POST['account_name01'])) $errors['account_name01'] = '氏（漢字）は必須です。';
        if (empty($_POST['account_name02'])) $errors['account_name02'] = '名（漢字）は必須です。';
        if (empty($_POST['account_birthday_year']) || empty($_POST['account_birthday_month']) || empty($_POST['account_birthday_day'])) {
            $errors['account_birthday'] = '生年月日は必須です。';
        }
        if (empty($_POST['account_jenda'])) $errors['account_jenda'] = '性別は必須です。';
        if (empty($_POST['account_bloodtype'])) $errors['account_bloodtype'] = '血液型は必須です。';

        // 電話番号のバリデーション
        if (!preg_match($patterns['half_width_numeric'], $_POST['account_tel01']) || !preg_match($patterns['half_width_numeric'], $_POST['account_tel02']) || !preg_match($patterns['half_width_numeric'], $_POST['account_tel03'])) {
            $errors['account_tel'] = '連絡先は半角数字のみで入力してください。';
        }

        // パスワードの処理
        if (!empty($_POST['account_password']) && $_POST['account_password'] !== $existing_password) {
            validate($_POST['account_password'], $patterns['password'], '半角英数字を含む8桁以上16桁以下で入力してください。', $errors, 'account_password');
            if (empty($errors['account_password'])) {
                $hashed_password = password_hash($_POST['account_password'], PASSWORD_DEFAULT);
                $password_sql = ', account_password = :account_password';
            }
        } else {
            $password_sql = '';  // パスワードが入力されていない場合、更新しない
        }

        // エラーがなければデータベースを更新
        if (empty($errors)) {
            // 更新SQL文
            $sql_update = '
                UPDATE accounts
                SET account_no = :account_no, account_salesoffice = :account_salesoffice, 
                    account_kana01 = :account_kana01, account_kana02 = :account_kana02,
                    account_name01 = :account_name01, account_name02 = :account_name02,
                    account_birthday = :account_birthday,
                    account_jenda = :account_jenda, account_bloodtype = :account_bloodtype,
                    account_zipcord = :account_zipcord,
                    account_pref = :account_pref, account_address01 = :account_address01,
                    account_address02 = :account_address02, account_address03 = :account_address03,
                    account_tel1 = :account_tel1, account_tel2 = :account_tel2,
                    account_license_expiration_date = :account_license_expiration_date,
                    account_guarentor_kana01 = :account_guarentor_kana01, account_guarentor_kana02 = :account_guarentor_kana02,
                    account_guarentor_name01 = :account_guarentor_name01, account_guarentor_name02 = :account_guarentor_name02,
                    account_relationship = :account_relationship,
                    account_guarentor_zipcode = :account_guarentor_zipcode, account_guarentor_pref = :account_guarentor_pref,
                    account_guarentor_address01 = :account_guarentor_address01, account_guarentor_address02 = :account_guarentor_address02, account_guarentor_address03 = :account_guarentor_address03,
                    account_guarentor_tel1 = :account_guarentor_tel1, account_guarentor_tel2 = :account_guarentor_tel2,
                    account_department = :account_department, account_workclass = :account_workclass, account_classification = :account_classification, account_enrollment = :account_enrollment,
                    account_employment_date = :account_employment_date,
                    account_appointment_date = :account_appointment_date,
                    account_retirement_date = :account_retirement_date
                    ' . $password_sql . ' 
                WHERE account_id = :account_id
            ';

            // ステートメント準備
            $stmt_update = $pdoAccount->prepare($sql_update);

            // 値をバインド
            $stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_birthday', sprintf('%04d-%02d-%02d', $_POST['account_birthday_year'], $_POST['account_birthday_month'], $_POST['account_birthday_day']), PDO::PARAM_STR);
            $stmt_update->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_zipcord', $_POST['account_zipcord01'] . '-' . $_POST['account_zipcord02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_tel1', $_POST['account_tel01'] . '-' . $_POST['account_tel02'] . '-' . $_POST['account_tel03'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_tel2', $_POST['account_tel04'] . '-' . $_POST['account_tel05'] . '-' . $_POST['account_tel06'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_license_expiration_date', sprintf('%04d-%02d-%02d', $_POST['account_license_expiration_date_year'], $_POST['account_license_expiration_date_month'], $_POST['account_license_expiration_date_day']), PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_zipcode', $_POST['account_guarentor_zipcord01'] . '-' . $_POST['account_guarentor_zipcord02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_tel1', $_POST['account_guarentor_tel01'] . '-' . $_POST['account_guarentor_tel02'] . '-' . $_POST['account_guarentor_tel03'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_guarentor_tel2', $_POST['account_guarentor_tel04'] . '-' . $_POST['account_guarentor_tel05'] . '-' . $_POST['account_guarentor_tel06'], PDO::PARAM_STR);
            $stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_INT);
            $stmt_update->bindValue(':account_employment_date', sprintf('%04d-%02d-%02d', $_POST['account_employment_date_year'], $_POST['account_employment_date_month'], $_POST['account_employment_date_day']), PDO::PARAM_STR);
            $stmt_update->bindValue(':account_appointment_date', sprintf('%04d-%02d-%02d', $_POST['account_appointment_date_year'], $_POST['account_appointment_date_month'], $_POST['account_appointment_date_day']), PDO::PARAM_STR);
            $stmt_update->bindValue(':account_retirement_date', sprintf('%04d-%02d-%02d', $_POST['account_retirement_date_year'], $_POST['account_retirement_date_month'], $_POST['account_retirement_date_day']), PDO::PARAM_STR);
            $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_INT);

            // パスワードがある場合にのみバインド
            if (!empty($password_sql)) {
                $stmt_update->bindValue(':account_password', $hashed_password, PDO::PARAM_STR);
            }

            // SQL文を実行して更新
            $stmt_update->execute();

            // メッセージを表示してリダイレクト
            $message = urlencode("従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に更新されました。");
            header("Location: list.php?message=$message");
            exit();
        }
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>従業員編集</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="register.css">
        <script src="https://yubinbango.github.io/yubinbango/yubinbango.js"></script>
    </head>

    <header>
        <h1>従業員編集</h1>
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
            <input type="hidden" name="account_id" value="<?= htmlspecialchars($account['account_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="first-table">
                    <tr>
                        <th>パスワード</th>
                        <td>
                            <input type="password" name="account_password" placeholder="パスワードを入力"
                                value="<?= htmlspecialchars($_POST['account_password'] ?? $account['account_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_password'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_password'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>従業員No<span class="required"> *</span></th>
                        <td>
                            <input type="text" name="account_no" value="<?= htmlspecialchars($_POST['account_no'] ?? $account['account_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_no'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>所属営業所<span class="required"> *</span></th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE, $account['account_salesoffice'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_salesoffice'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_salesoffice'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" 
                                value="<?= htmlspecialchars($_POST['account_kana01'] ?? $account['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" 
                                value="<?= htmlspecialchars($_POST['account_kana02'] ?? $account['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    <tr>
                        <th>氏（漢字）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_name01" 
                                value="<?= htmlspecialchars($_POST['account_name01'] ?? $account['account_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_name01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_name01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（漢字）<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="text" placeholder="一番" name="account_name02" 
                                value="<?= htmlspecialchars($_POST['account_name02'] ?? $account['account_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_name02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_name02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>生年月日<span class="required"> *</span></th>
                        <td colspan="2">
                            <select name="account_birthday_year">
                                <?php
                                    $startYear = 1939;  
                                    $endYear = date("Y") - 25;  // 現在の年から25年前を終了年に設定
                                    echo generateYearOptions($startYear, $endYear, $_POST['account_birthday_year'] ?? $birthday_year);
                                ?>
                            </select>年
                            <select name="account_birthday_month">
                                <?php
                                    echo generateMonthOptions($_POST['account_birthday_month'] ?? $birthday_month);
                                ?>
                            </select>月  
                            <select name="account_birthday_day">
                                <?php
                                    echo generateDayOptions($_POST['account_birthday_day'] ?? $birthday_day);
                                ?>
                            </select>日
                            <?php if (isset($errors['account_birthday'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_birthday'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr> 
                    <tr>
                        <th>性別<span class="required"> *</span></th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA, $_POST['account_jenda'] ?? $account['account_jenda'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_jenda'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_jenda'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>血液型<span class="required"> *</span></th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE, $_POST['account_bloodtype'] ?? $account['account_bloodtype'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_bloodtype'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_bloodtype'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="123" 
                                value="<?= htmlspecialchars($_POST['account_zipcord01'] ?? $zipcode01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="4567" 
                                value="<?= htmlspecialchars($_POST['account_zipcord02'] ?? $zipcode02, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                    <th>都道府県<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="p-region" name="account_pref" 
                                value="<?= htmlspecialchars($_POST['account_pref'] ?? $account['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_pref'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_pref'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>市町村区<span class="required"> *</span></th>
                        <td>
                            <input type="text" class="p-locality" name="account_address01" 
                                value="<?= htmlspecialchars($_POST['account_address01'] ?? $account['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_address01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>町名番地<span class="required"> *</span></th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_address02" placeholder="駒形通2丁目2-25" 
                                value="<?= htmlspecialchars($_POST['account_address02'] ?? $account['account_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_address03" placeholder="辰巳マンション1111号室" 
                                value="<?= htmlspecialchars($_POST['account_address03'] ?? $account['account_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_address03'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1<span class="required"> *</span></th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="090" 
                                value="<?= htmlspecialchars($_POST['account_tel01'] ?? $tel01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel02'] ?? $tel02, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel03'] ?? $tel03, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel04" size="4" maxlength="4" name="account_tel04" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_tel04'] ?? $tel04, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel05" size="4" maxlength="4" name="account_tel05" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel05'] ?? $tel05, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel06" size="4" maxlength="4" name="account_tel06" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel06'] ?? $tel06, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel2'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限<span class="required"> *</span></th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $currentYear = date('Y');
                                    $endYear = $currentYear + 10; // 現在の年から10年後まで
                                    echo generateYearOptions($currentYear, $endYear, $_POST['account_license_expiration_date_year'] ?? $license_expiration_year);
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?= generateMonthOptions($_POST['account_license_expiration_date_month'] ?? $license_expiration_month); ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?= generateDayOptions($_POST['account_license_expiration_date_day'] ?? $license_expiration_day); ?>
                            </select>日
                            <?php if (isset($errors['account_license_expiration_date'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_license_expiration_date'], ENT_QUOTES, 'UTF-8'); ?></span>
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
                            <input type="text" class="text" placeholder="たつみ" name="account_guarentor_kana01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_kana01'] ?? $account['account_guarentor_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>身元保証人<br>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="おやじ" name="account_guarentor_kana02" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_kana02'] ?? $account['account_guarentor_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_kana02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>身元保証人<br>氏（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_guarentor_name01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_name01'] ?? $account['account_guarentor_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>身元保証人<br>名（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="親父" name="account_guarentor_name02" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_name02'] ?? $account['account_guarentor_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>続柄</th>
                        <td>
                            <input type="text" class="text" placeholder="父" name="account_relationship" 
                                value="<?= htmlspecialchars($_POST['account_relationship'] ?? $account['account_relationship'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_guarentor_zipcord01" placeholder="123"
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord01'] ?? $guarentor_zipcode01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_guarentor_zipcord02" placeholder="4567"
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord02'] ?? $guarentor_zipcode02, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_zipcord'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_guarentor_pref" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_pref'] ?? $account['account_guarentor_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_guarentor_address01" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address01'] ?? $account['account_guarentor_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
                        <td colspan="3">
                            <input type="text" class="p-street-address" name="account_guarentor_address02" placeholder="駒形通2丁目2-25" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address02'] ?? $account['account_guarentor_address02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">                            <?php if (isset($errors['account_guarentor_address02'])): ?>
                                    <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_address02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>マンション名など</th>
                        <td colspan="3">
                            <input type="text" class="p-extended-address" name="account_guarentor_address03" placeholder="辰巳マンション1111号室" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_address03'] ?? $account['account_guarentor_address03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_address03'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_address03'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel01" placeholder="090"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel01'] ?? $guarentor_tel01, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel02" placeholder="1234"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel02'] ?? $guarentor_tel02, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel03" placeholder="5678"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel03'] ?? $guarentor_tel03, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel1'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_tel1'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel04" placeholder="080"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel04'] ?? $guarentor_tel04, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel05" placeholder="5678"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel05'] ?? $guarentor_tel05, ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" size="4" maxlength="4" name="account_guarentor_tel06" placeholder="1234"
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel06'] ?? $guarentor_tel06, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel2'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <table class="third-table">
                    <tr>
                        <th>所属課<span class="required"> *</span></th>
                        <td>
                            <select name="account_department">
                                <?= generateSelectOptions(ACCOUNT_DEPARTMENT, $_POST['account_department'] ?? $account['account_department'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_department'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_department'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>勤務区分<span class="required"> *</span></th>
                        <td>
                            <select name="account_workclass">
                                <?= generateSelectOptions(ACCOUNT_WORKCLASS, $_POST['account_workclass'] ?? $account['account_workclass'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_workclass'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_workclass'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>職種区分<span class="required"> *</span></th>
                        <td>
                            <select name="account_classification">
                                <?= generateSelectOptions(ACCOUNT_CLASSIFICATION, $_POST['account_classification'] ?? $account['account_classification'] ?? '') ?>
                            </select>
                            <?php if (isset($errors['account_classification'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_classification'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>在籍区分</th>
                        <td>
                            <select name="account_enrollment">
                                <?= generateSelectOptions(ACCOUNT_ENROLLMENT, $_POST['account_enrollment'] ?? $account['account_enrollment'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>雇用年月日</th>
                        <td colspan="2">
                            <select name="account_employment_date_year">
                                <?php
                                    echo generateYearOptions(1930, date("Y"), $_POST['account_employment_date_year'] ?? $employment_year);
                                ?>
                            </select>年
                            <select name="account_employment_date_month">
                                <?= generateMonthOptions($_POST['account_employment_date_month'] ?? $employment_month); ?>
                            </select>月
                            <select name="account_employment_date_day">
                                <?= generateDayOptions($_POST['account_employment_date_day'] ?? $employment_day); ?>
                            </select>日
                        </td>
                    </tr>
                    <tr>
                        <th>選任年月日</th>
                        <td colspan="2">
                            <select name="account_appointment_date_year">
                                <?php
                                    echo generateYearOptions(1930, date("Y"), $_POST['account_appointment_date_year'] ?? $appointment_year);
                                ?>
                            </select>年
                            <select name="account_appointment_date_month">
                                <?= generateMonthOptions($_POST['account_appointment_date_month'] ?? $appointment_month); ?>
                            </select>月
                            <select name="account_appointment_date_day">
                                <?= generateDayOptions($_POST['account_appointment_date_day'] ?? $appointment_day); ?>
                            </select>日
                        </td>
                    </tr>
                    <tr>
                        <th>退職年月日</th>
                        <td colspan="2">
                            <select name="account_retirement_date_year">
                                <?php
                                    echo generateYearOptions(1930, date("Y"), $_POST['account_retirement_date_year'] ?? $retirement_year);
                                ?>
                            </select>年
                            <select name="account_retirement_date_month">
                                <?= generateMonthOptions($_POST['account_retirement_date_month'] ?? $retirement_month); ?>
                            </select>月
                            <select name="account_retirement_date_day">
                                <?= generateDayOptions($_POST['account_retirement_date_day'] ?? $retirement_day); ?>
                            </select>日
                        </td>
                    </tr>                       
                </table>
            </div>    
            
            <!-- ログインユーザーの勤務区分が「管理者」または「役員」の場合のみ、更新ボタンを表示 -->
            <?php if (isset($logged_in_workclass) && ($logged_in_workclass === 1 || $logged_in_workclass === 2)): ?>
                <div class="flex">
                    <input type="submit" name="submit" value="更新">
                </div>
            <?php endif; ?>

        </form>

    </body>
    
</html>
