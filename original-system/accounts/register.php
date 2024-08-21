<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';
$errors = []; // エラーメッセージを格納する配列
require '../config/config.php';  // config.php をインクルード
echo date('Y-m-d H:i:s');

// バリデーション関数
function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
    if (!empty($input) && !preg_match($pattern, $input)) {
        $errors[$fieldName] = $errorMessage;
    }
}

// データベース接続
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}

// POSTリクエストがある場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // バリデーションパターンの定義
    $patterns = [
        'half_width_numeric' => '/^\d+$/',
        'hiragana' => '/^[ぁ-んー　]+$/u',
        'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
        'address_kanji_hiragana_english' => '/^[\p{Han}\p{Hiragana}A-Z\d!@#$%^&*()\-_=+{};:,<.>]+$/u',
        'address_kanji_hiragana_katakana_english' => '/^[\p{Han}\p{Hiragana}\p{Katakana}A-Za-z0-9!@#$%^&*()\-_=+{};:,<.>]+$/u',
    ];

    // パスワード処理
    $password_sql = '';
    $password_placeholder = '';
    $hashed_password = null;

    if (!empty($_POST['account_password'])) {
        // パスワードが入力されている場合のみバリデーションを行う
        validate($_POST['account_password'], $patterns['password'], '半角英数字を含む8桁以上16桁以下で入力してください。', $errors, 'account_password');

        if (empty($errors['account_password'])) {
            $hashed_password = password_hash($_POST['account_password'], PASSWORD_DEFAULT);
            $password_sql = ', account_password';
            $password_placeholder = ', :account_password';
        }
    }

    // バリデーション処理
    validate($_POST['account_no'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $errors, 'account_no');
    validate($_POST['account_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana01');
    validate($_POST['account_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana02');

    // 必須項目のバリデーション
    if (empty($_POST['account_no'])) {
        $errors['account_no'] = '従業員Noは必須です。';
    }
    if (empty($_POST['account_department'])) {
        $errors['account_department'] = '所属課は必須です。';
    }
    if (empty($_POST['account_workclass'])) {
        $errors['account_workclass'] = '勤務区分は必須です。';
    }
    if (empty($_POST['account_classification'])) {
        $errors['account_classification'] = '職種区分は必須です。';
    }

    // account_no が既に存在するか確認
    if (!isset($errors['account_no']) && !empty($_POST['account_no'])) {
        try {
            $sql = 'SELECT COUNT(*) FROM accounts WHERE account_no = :account_no';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors['account_no'] = 'この従業員Noは既に存在します。別の番号を入力してください。';
            }
        } catch (PDOException $e) {
            $errors['account_no'] = 'データベースエラーが発生しました: ' . $e->getMessage();
        }
    }

    // 郵便番号バリデーション
    $zip_errors = [];
    validate($_POST['account_zipcord01'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord01');
    validate($_POST['account_zipcord02'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $zip_errors, 'account_zipcord02');
    if (!empty($zip_errors)) {
        $errors['account_zipcord'] = implode('<br>', $zip_errors);
    }

    // 住所バリデーション
    validate($_POST['account_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_address02');
    validate($_POST['account_address03'] ?? '', $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_address03');

    // 電話番号バリデーション
    $tel_errors = [];
    validate($_POST['account_tel01'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel01');
    validate($_POST['account_tel02'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel02');
    validate($_POST['account_tel03'] ?? '', $patterns['half_width_numeric'], '', $tel_errors, 'account_tel03');
    if (!empty($tel_errors)) {
        $errors['account_tel'] = '半角数字のみで入力してください。';
    }

    // 連絡先2のバリデーション
    $tel2_errors = [];
    validate($_POST['account_tel04'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel04');
    validate($_POST['account_tel05'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel05');
    validate($_POST['account_tel06'] ?? '', $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel06');
    if (!empty($tel2_errors)) {
        $errors['account_tel2'] = '半角数字のみで入力してください。';
    }

    // 保証人の情報バリデーション
    validate($_POST['account_guarentor_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana01');
    validate($_POST['account_guarentor_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana02');

    // 保証人の郵便番号バリデーション
    $guarentor_zip_errors = [];
    validate($_POST['account_guarentor_zipcord01'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord01');
    validate($_POST['account_guarentor_zipcord02'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord02');
    if (!empty($guarentor_zip_errors)) {
        $errors['account_guarentor_zipcord'] = implode('<br>', $guarentor_zip_errors);
    }

    // 保証人の住所バリデーション
    validate($_POST['account_guarentor_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_guarentor_address02');
    validate($_POST['account_guarentor_address03'] ?? '', $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_guarentor_address03');

    // 保証人の連絡先バリデーション
    $guarentor_tel_errors = [];
    validate($_POST['account_guarentor_tel01'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel01');
    validate($_POST['account_guarentor_tel02'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel02');
    validate($_POST['account_guarentor_tel03'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel03');
    if (!empty($guarentor_tel_errors)) {
        $errors['account_guarentor_tel'] = '半角数字のみで入力してください。';
    }

    // 保証人の連絡先2バリデーション
    $guarentor_tel2_errors = [];
    validate($_POST['account_guarentor_tel04'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel04');
    validate($_POST['account_guarentor_tel05'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel05');
    validate($_POST['account_guarentor_tel06'] ?? '', $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel06');
    if (!empty($guarentor_tel2_errors)) {
        $errors['account_guarentor_tel2'] = '半角数字のみで入力してください。';
    }

    if (empty($errors)) {
        try {
            // パスワードが入力されている場合のみバインド
            if ($hashed_password) {
                $sql = '
                    INSERT INTO accounts (account_no, account_salesoffice, account_kana01, account_kana02, account_name01, account_name02,
                                          account_birthday_year, account_birthday_month, account_birthday_day, account_jenda, account_bloodtype, 
                                          account_zipcord01, account_zipcord02, account_pref, account_address01, account_address02, account_address03,
                                          account_tel01, account_tel02, account_tel03, account_tel04, account_tel05, account_tel06,
                                          account_license_expiration_date_year, account_license_expiration_date_month, account_license_expiration_date_day,
                                          account_guarentor_kana01, account_guarentor_kana02, account_guarentor_name01, account_guarentor_name02, account_relationship,
                                          account_guarentor_zipcord01, account_guarentor_zipcord02, account_guarentor_pref, account_guarentor_address01, account_guarentor_address02, account_guarentor_address03,
                                          account_guarentor_tel01, account_guarentor_tel02, account_guarentor_tel03, account_guarentor_tel04, account_guarentor_tel05, account_guarentor_tel06,
                                          account_department, account_workclass, account_classification, account_enrollment,  
                                          account_employment_year, account_employment_month, account_employment_day, account_appointment_year, account_appointment_month, account_appointment_day,    
                                          account_retirement_year, account_retirement_month, account_retirement_day, account_password)
                    VALUES (:account_no, :account_salesoffice, :account_kana01, :account_kana02, :account_name01, :account_name02,
                            :account_birthday_year, :account_birthday_month, :account_birthday_day, :account_jenda, :account_bloodtype,       
                            :account_zipcord01, :account_zipcord02, :account_pref, :account_address01, :account_address02, :account_address03,
                            :account_tel01, :account_tel02, :account_tel03, :account_tel04, :account_tel05, :account_tel06,
                            :account_license_expiration_date_year, :account_license_expiration_date_month, :account_license_expiration_date_day,
                            :account_guarentor_kana01, :account_guarentor_kana02, :account_guarentor_name01, :account_guarentor_name02, :account_relationship,
                            :account_guarentor_zipcord01, :account_guarentor_zipcord02, :account_guarentor_pref, :account_guarentor_address01, :account_guarentor_address02, :account_guarentor_address03,
                            :account_guarentor_tel01, :account_guarentor_tel02, :account_guarentor_tel03, :account_guarentor_tel04, :account_guarentor_tel05, :account_guarentor_tel06,
                            :account_department, :account_workclass, :account_classification, :account_enrollment,  
                            :account_employment_year, :account_employment_month, :account_employment_day, :account_appointment_year, :account_appointment_month, :account_appointment_day,    
                            :account_retirement_year, :account_retirement_month, :account_retirement_day, :account_password)';
            } else {
                // パスワードがない場合のSQL文
                $sql = '
                    INSERT INTO accounts (account_no, account_salesoffice, account_kana01, account_kana02, account_name01, account_name02,
                                          account_birthday_year, account_birthday_month, account_birthday_day, account_jenda, account_bloodtype, 
                                          account_zipcord01, account_zipcord02, account_pref, account_address01, account_address02, account_address03,
                                          account_tel01, account_tel02, account_tel03, account_tel04, account_tel05, account_tel06,
                                          account_license_expiration_date_year, account_license_expiration_date_month, account_license_expiration_date_day,
                                          account_guarentor_kana01, account_guarentor_kana02, account_guarentor_name01, account_guarentor_name02, account_relationship,
                                          account_guarentor_zipcord01, account_guarentor_zipcord02, account_guarentor_pref, account_guarentor_address01, account_guarentor_address02, account_guarentor_address03,
                                          account_guarentor_tel01, account_guarentor_tel02, account_guarentor_tel03, account_guarentor_tel04, account_guarentor_tel05, account_guarentor_tel06,
                                          account_department, account_workclass, account_classification, account_enrollment,  
                                          account_employment_year, account_employment_month, account_employment_day, account_appointment_year, account_appointment_month, account_appointment_day,    
                                          account_retirement_year, account_retirement_month, account_retirement_day)
                    VALUES (:account_no, :account_salesoffice, :account_kana01, :account_kana02, :account_name01, :account_name02,
                            :account_birthday_year, :account_birthday_month, :account_birthday_day, :account_jenda, :account_bloodtype,       
                            :account_zipcord01, :account_zipcord02, :account_pref, :account_address01, :account_address02, :account_address03,
                            :account_tel01, :account_tel02, :account_tel03, :account_tel04, :account_tel05, :account_tel06,
                            :account_license_expiration_date_year, :account_license_expiration_date_month, :account_license_expiration_date_day,
                            :account_guarentor_kana01, :account_guarentor_kana02, :account_guarentor_name01, :account_guarentor_name02, :account_relationship,
                            :account_guarentor_zipcord01, :account_guarentor_zipcord02, :account_guarentor_pref, :account_guarentor_address01, :account_guarentor_address02, :account_guarentor_address03,
                            :account_guarentor_tel01, :account_guarentor_tel02, :account_guarentor_tel03, :account_guarentor_tel04, :account_guarentor_tel05, :account_guarentor_tel06,
                            :account_department, :account_workclass, :account_classification, :account_enrollment,  
                            :account_employment_year, :account_employment_month, :account_employment_day, :account_appointment_year, :account_appointment_month, :account_appointment_day,    
                            :account_retirement_year, :account_retirement_month, :account_retirement_day)';
            }
            
            $stmt = $pdo->prepare($sql);

            // Bind values
            $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR); // 従業員No
            $stmt->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属営業所
            $stmt->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ふりがな）
            $stmt->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ふりがな）
            $stmt->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
            $stmt->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
            $stmt->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_STR); // 生年月日（年）
            $stmt->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_STR); // 生年月日（月）
            $stmt->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_STR); // 生年月日（日）
            $stmt->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_STR); // 性別
            $stmt->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_STR); // 血液型
            $stmt->bindValue(':account_zipcord01', $_POST['account_zipcord01'], PDO::PARAM_STR); // 郵便番号（前半）
            $stmt->bindValue(':account_zipcord02', $_POST['account_zipcord02'], PDO::PARAM_STR); // 郵便番号（後半）
            $stmt->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR); // 都道府県
            $stmt->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR); // 市町村区
            $stmt->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR); // 町名番地
            $stmt->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR); // マンション名など
            $stmt->bindValue(':account_tel01', $_POST['account_tel01'], PDO::PARAM_STR); // 連絡先1（前半）
            $stmt->bindValue(':account_tel02', $_POST['account_tel02'], PDO::PARAM_STR); // 連絡先1（中）
            $stmt->bindValue(':account_tel03', $_POST['account_tel03'], PDO::PARAM_STR); // 連絡先1（後半）
            $stmt->bindValue(':account_tel04', $_POST['account_tel04'], PDO::PARAM_STR); // 連絡先2（前半）
            $stmt->bindValue(':account_tel05', $_POST['account_tel05'], PDO::PARAM_STR); // 連絡先2（中）
            $stmt->bindValue(':account_tel06', $_POST['account_tel06'], PDO::PARAM_STR); // 連絡先2（後半）
            $stmt->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_STR); // 免許証有効期限（年）
            $stmt->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_STR); // 免許証有効期限（月）
            $stmt->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_STR); // 免許証有効期限（日）
            $stmt->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR); // 身元保証人氏（ふりがな）
            $stmt->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR); // 身元保証人名（ふりがな）
            $stmt->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR); // 身元保証人氏（漢字）
            $stmt->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR); // 身元保証人名（漢字）
            $stmt->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR); // 続柄
            $stmt->bindValue(':account_guarentor_zipcord01', $_POST['account_guarentor_zipcord01'], PDO::PARAM_STR); // 身元保証人郵便番号（前半）
            $stmt->bindValue(':account_guarentor_zipcord02', $_POST['account_guarentor_zipcord02'], PDO::PARAM_STR); // 身元保証人郵便番号（後半）
            $stmt->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR); // 身元保証人都道府県
            $stmt->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR); // 身元保証人市町村区
            $stmt->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR); // 身元保証人町名番地
            $stmt->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR); // 身元保証人マンション名など
            $stmt->bindValue(':account_guarentor_tel01', $_POST['account_guarentor_tel01'], PDO::PARAM_STR); // 身元保証人連絡先1（前半）
            $stmt->bindValue(':account_guarentor_tel02', $_POST['account_guarentor_tel02'], PDO::PARAM_STR); // 身元保証人連絡先1（中）
            $stmt->bindValue(':account_guarentor_tel03', $_POST['account_guarentor_tel03'], PDO::PARAM_STR); // 身元保証人連絡先1（後半）
            $stmt->bindValue(':account_guarentor_tel04', $_POST['account_guarentor_tel04'], PDO::PARAM_STR); // 身元保証人連絡先2（前半）
            $stmt->bindValue(':account_guarentor_tel05', $_POST['account_guarentor_tel05'], PDO::PARAM_STR); // 身元保証人連絡先2（中）
            $stmt->bindValue(':account_guarentor_tel06', $_POST['account_guarentor_tel06'], PDO::PARAM_STR); // 身元保証人連絡先2（後半）
            $stmt->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_STR); // 所属課
            $stmt->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_STR); // 勤務区分
            $stmt->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_STR); // 職種区分
            $stmt->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_STR); // 在籍区分
            $stmt->bindValue(':account_employment_year', $_POST['account_employment_year'], PDO::PARAM_STR); // 雇用年月日（年）
            $stmt->bindValue(':account_employment_month', $_POST['account_employment_month'], PDO::PARAM_STR); // 雇用年月日（月）
            $stmt->bindValue(':account_employment_day', $_POST['account_employment_day'], PDO::PARAM_STR); // 雇用年月日（日）
            $stmt->bindValue(':account_appointment_year', $_POST['account_appointment_year'], PDO::PARAM_STR); // 選任年月日（年）
            $stmt->bindValue(':account_appointment_month', $_POST['account_appointment_month'], PDO::PARAM_STR); // 選任年月日（月）
            $stmt->bindValue(':account_appointment_day', $_POST['account_appointment_day'], PDO::PARAM_STR); // 選任年月日（日）
            $stmt->bindValue(':account_retirement_year', $_POST['account_retirement_year'], PDO::PARAM_STR); // 退職年月日（年）
            $stmt->bindValue(':account_retirement_month', $_POST['account_retirement_month'], PDO::PARAM_STR); // 退職年月日（月）
            $stmt->bindValue(':account_retirement_day', $_POST['account_retirement_day'], PDO::PARAM_STR); // 退職年月日（日）

            // パスワードが入力されている場合のみバインド
            if ($hashed_password) {
                $stmt->bindValue(':account_password', $hashed_password, PDO::PARAM_STR);
            }

            // SQL文を実行する
            $stmt->execute();

            // メッセージを設定してリダイレクト
            $message = "従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に登録されました。";
            header("Location: list.php?message=" . urlencode($message));
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
                        <th>所属営業所</th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" value="<?= htmlspecialchars($_POST['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" value="<?= htmlspecialchars($_POST['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（漢字）</th>
                        <td><input type="text" class="text" placeholder="辰巳" name="account_name01"></td>
                        <th>名（漢字）</th>
                        <td><input type="text" class="text" placeholder="一番" name="account_name02"></td>
                    </tr>
                    <tr>
                        <th>生年月日</th>
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
                        </td>
                    </tr>
                    <tr>
                        <th>性別</th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA); ?>
                            </select>
                        </td>
                        <th>血液型</th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="420" value="<?= htmlspecialchars($_POST['account_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="0042" value="<?= htmlspecialchars($_POST['account_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?php echo htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_pref" value="<?= htmlspecialchars($_POST['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_address01" value="<?= htmlspecialchars($_POST['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
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
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="0120" value="<?= htmlspecialchars($_POST['account_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" value="<?= htmlspecialchars($_POST['account_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" value="<?= htmlspecialchars($_POST['account_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
                        <th>免許証有効期限</th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $currentYear = 2024;
                                    $endYear = $currentYear + 10;  // 現在の年から10年後まで(自動的に10年追加される)
                                        echo generateYearOptions($currentYear, $endYear, $_POST['account_license_expiration_date_year'] ?? '');
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?= generateMonthOptions($_POST['account_license_expiration_date_month'] ?? '') ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?= generateDayOptions($_POST['account_license_expiration_date_day'] ?? '') ?>
                            </select>日
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