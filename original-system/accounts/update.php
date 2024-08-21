
<?php
session_start();

$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';
$errors = []; // エラーメッセージを格納する配列
$logged_in_workclass = $_SESSION['account']['workclass'] ?? null;
require '../config/config.php';  // config.php をインクルード
echo date('Y-m-d H:i:s');

// データベース接続
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // アカウントIDが指定されている場合、アカウント情報を取得
    if (isset($_GET['account_id'])) {
        $sql = 'SELECT * FROM accounts WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ログインユーザーの勤務区分を取得（勤務区分ごと更新などの制限をかけるため）
    // $logged_in_workclass = $_SESSION['account']['workclass'] ?? 0;

    // validate() 関数を定義
    function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
        if (empty($input) || !preg_match($pattern, $input)) {
            $errors[$fieldName] = $errorMessage;
        }
    }

    // フォームが送信された場合、アカウント情報を更新
    if (isset($_POST['submit'])) {

        // バリデーションパターンの定義
        $patterns = [
            'half_width_numeric' => '/^\d+$/',
            'hiragana' => '/^[ぁ-んー　]+$/u',
            'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
            'address_kanji_hiragana_english' => '/^[\p{Han}\p{Hiragana}A-Z\d!@#$%^&*()\-_=+{};:,<.>]+$/u',
            'address_kanji_hiragana_katakana_english' => '/^[\p{Han}\p{Hiragana}\p{Katakana}A-Za-z0-9!@#$%^&*()\-_=+{};:,<.>]+$/u',
        ];

        $password_sql = '';
        $hashed_password = null;

        // ---- バリデーション処理 ----
        if (isset($_POST['account_password'])) {
            if ($_POST['account_password'] === '') {
                // パスワードが空白ならNULLに設定
                $password_sql = ', account_password = NULL';
            } elseif ($_POST['account_password'] !== $account['account_password']) {
                validate($_POST['account_password'], $patterns['password'], '半角英数字を含む8桁以上16桁以下で入力してください。', $errors, 'account_password');
                if (empty($errors['account_password'])) {
                    $hashed_password = password_hash($_POST['account_password'], PASSWORD_DEFAULT);
                    $password_sql = ', account_password = :account_password';
                }
            }
        }
        // 従業員Noバリテーション
        validate($_POST['account_no'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $errors, 'account_no');
        validate($_POST['account_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana01');
        validate($_POST['account_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana02');
        // account_no が既に存在するか確認 (更新時、現在のアカウントIDは除外)
        if (!isset($errors['account_no']) && !empty($_POST['account_no'])) {
            try {
                $sql = 'SELECT COUNT(*) FROM accounts WHERE account_no = :account_no AND account_id != :account_id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR);
                $stmt->bindValue(':account_id', $_GET['account_id'], PDO::PARAM_INT);
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

        // 住所バリデーション（account_address03は、空の場合、バリテーションをスキップ）
        validate($_POST['account_address02'] ?? '', $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_address02');
        if (!empty($_POST['account_address03'])) {
            validate($_POST['account_address03'], $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_address03');
        }
        
        // 電話番号バリデーション（空の場合、バリテーションをスキップ）
        $tel2_errors = [];
        if (!empty($_POST['account_tel01'])) {
            validate($_POST['account_tel01'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel01');
        }
        if (!empty($_POST['account_tel02'])) {
            validate($_POST['account_tel02'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel02');
        }
        if (!empty($_POST['account_tel03'])) {
            validate($_POST['account_tel03'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel03');
        }
        if (!empty($tel2_errors)) {
            $errors['account_tel1'] = '半角数字のみで入力してください。';
        }

        // 連絡先2のバリデーション（空の場合、バリテーションをスキップ）
        $tel2_errors = [];
        if (!empty($_POST['account_tel04'])) {
            validate($_POST['account_tel04'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel04');
        }
        if (!empty($_POST['account_tel05'])) {
            validate($_POST['account_tel05'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel05');
        }
        if (!empty($_POST['account_tel06'])) {
            validate($_POST['account_tel06'], $patterns['half_width_numeric'], '', $tel2_errors, 'account_tel06');
        }
        if (!empty($tel2_errors)) {
            $errors['account_tel2'] = '半角数字のみで入力してください。';
        }

        // 保証人氏名（ひらがな）バリデーション（空の場合は、バリテーションをスキップ）
        if (!empty($_POST['account_guarentor_kana01'])) {
            validate($_POST['account_guarentor_kana01'], $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana01');
        }

        if (!empty($_POST['account_guarentor_kana02'])) {
            validate($_POST['account_guarentor_kana02'], $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_guarentor_kana02');
        }

        // 保証人の郵便番号バリデーション（空の場合は、バリテーションをスキップ）
        $guarentor_zip_errors = [];
        if (!empty($_POST['account_guarentor_zipcord01']) || !empty($_POST['account_guarentor_zipcord02'])) {
            if (!empty($_POST['account_guarentor_zipcord01'])) {
                validate($_POST['account_guarentor_zipcord01'], $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord01');
            }
            if (!empty($_POST['account_guarentor_zipcord02'])) {
                validate($_POST['account_guarentor_zipcord02'], $patterns['half_width_numeric'], '半角数字のみで入力してください。', $guarentor_zip_errors, 'account_guarentor_zipcord02');
            }
            if (!empty($guarentor_zip_errors)) {
                $errors['account_guarentor_zipcord'] = implode('<br>', $guarentor_zip_errors);
            }
        }

        // 保証人の住所バリデーション（空の場合は、バリテーションをスキップ）
        if (!empty($_POST['account_guarentor_address02'])) {
            validate($_POST['account_guarentor_address02'], $patterns['address_kanji_hiragana_english'], '数字および記号は半角のみで入力してください。', $errors, 'account_guarentor_address02');
        }
        if (!empty($_POST['account_guarentor_address03'])) {
            validate($_POST['account_guarentor_address03'], $patterns['address_kanji_hiragana_katakana_english'], '数字と記号およびアルファベットは半角で入力してください。', $errors, 'account_guarentor_address03');
        }

        // 保証人の連絡先1バリデーション（空の場合、バリテーションをスキップ）
        $guarentor_tel_errors = [];

        if (!empty($_POST['account_guarentor_tel01'])) {
            validate($_POST['account_guarentor_tel01'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel01');
        }

        if (!empty($_POST['account_guarentor_tel02'])) {
            validate($_POST['account_guarentor_tel02'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel02');
        }

        if (!empty($_POST['account_guarentor_tel03'])) {
            validate($_POST['account_guarentor_tel03'], $patterns['half_width_numeric'], '', $guarentor_tel_errors, 'account_guarentor_tel03');
        }

        if (!empty($guarentor_tel_errors)) {
            $errors['account_guarentor_tel'] = '半角数字のみで入力してください。';
        }

        // 保証人の連絡先2バリデーション（空の場合、バリテーションをスキップ）
        $guarentor_tel2_errors = [];
        if (!empty($_POST['account_guarentor_tel04'])) {
            validate($_POST['account_guarentor_tel04'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel04');
        }
        if (!empty($_POST['account_guarentor_tel05'])) {
            validate($_POST['account_guarentor_tel05'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel05');
        }
        if (!empty($_POST['account_guarentor_tel06'])) {
            validate($_POST['account_guarentor_tel06'], $patterns['half_width_numeric'], '', $guarentor_tel2_errors, 'account_guarentor_tel06');
        }
        if (!empty($guarentor_tel2_errors)) {
            $errors['account_guarentor_tel2'] = '半角数字のみで入力してください。';
        }



        // ---- バリテーション処理コードの終了----

        // すべてのエラーチェックが終わった後、エラーがなければデータベースを更新
        if (empty($errors)) {
            $sql_update = '
            UPDATE accounts
            SET account_no = :account_no, account_salesoffice = :account_salesoffice, account_kana01 = :account_kana01, account_kana02 = :account_kana02,
                account_name01 = :account_name01, account_name02 = :account_name02, account_birthday_year = :account_birthday_year, account_birthday_month = :account_birthday_month, account_birthday_day = :account_birthday_day,
                account_jenda = :account_jenda, account_bloodtype = :account_bloodtype, account_zipcord01 = :account_zipcord01, account_zipcord02 = :account_zipcord02,
                account_pref = :account_pref, account_address01 = :account_address01, account_address02 = :account_address02, account_address03 = :account_address03,
                account_tel01 = :account_tel01, account_tel02 = :account_tel02, account_tel03 = :account_tel03, account_tel04 = :account_tel04, account_tel05 = :account_tel05, account_tel06 = :account_tel06,   
                account_license_expiration_date_year = :account_license_expiration_date_year, account_license_expiration_date_month = :account_license_expiration_date_month, account_license_expiration_date_day = :account_license_expiration_date_day,
                account_guarentor_kana01 = :account_guarentor_kana01, account_guarentor_kana02 = :account_guarentor_kana02, account_guarentor_name01 = :account_guarentor_name01, account_guarentor_name02 = :account_guarentor_name02,
                account_relationship = :account_relationship, account_guarentor_zipcord01 = :account_guarentor_zipcord01, account_guarentor_zipcord02 = :account_guarentor_zipcord02,
                account_guarentor_pref = :account_guarentor_pref, account_guarentor_address01 = :account_guarentor_address01, account_guarentor_address02 = :account_guarentor_address02, account_guarentor_address03 = :account_guarentor_address03,
                account_guarentor_tel01 = :account_guarentor_tel01, account_guarentor_tel02 = :account_guarentor_tel02, account_guarentor_tel03 = :account_guarentor_tel03, account_guarentor_tel04 = :account_guarentor_tel04, account_guarentor_tel05 = :account_guarentor_tel05, account_guarentor_tel06 = :account_guarentor_tel06,
                account_department = :account_department, account_workclass = :account_workclass, account_classification = :account_classification, account_enrollment = :account_enrollment,
                account_employment_year = :account_employment_year, account_employment_month = :account_employment_month, account_employment_day = :account_employment_day,
                account_appointment_year = :account_appointment_year, account_appointment_month = :account_appointment_month, account_appointment_day = :account_appointment_day,
                account_retirement_year = :account_retirement_year, account_retirement_month = :account_retirement_month, account_retirement_day = :account_retirement_day
                ' . $password_sql . ' 
            WHERE account_id = :account_id
            ';
            $stmt_update = $pdo->prepare($sql_update);

            // Bind values
            $stmt_update->bindValue(':account_no', $_POST['account_no'], PDO::PARAM_STR); // 従業員No
            $stmt_update->bindValue(':account_salesoffice', $_POST['account_salesoffice'], PDO::PARAM_STR); // 所属営業所
            $stmt_update->bindValue(':account_kana01', $_POST['account_kana01'], PDO::PARAM_STR); // 氏（ふりがな）
            $stmt_update->bindValue(':account_kana02', $_POST['account_kana02'], PDO::PARAM_STR); // 名（ふりがな）
            $stmt_update->bindValue(':account_name01', $_POST['account_name01'], PDO::PARAM_STR); // 氏（漢字）
            $stmt_update->bindValue(':account_name02', $_POST['account_name02'], PDO::PARAM_STR); // 名（漢字）
            $stmt_update->bindValue(':account_birthday_year', $_POST['account_birthday_year'], PDO::PARAM_STR); // 生年月日（年）
            $stmt_update->bindValue(':account_birthday_month', $_POST['account_birthday_month'], PDO::PARAM_STR); // 生年月日（月）
            $stmt_update->bindValue(':account_birthday_day', $_POST['account_birthday_day'], PDO::PARAM_STR); // 生年月日（日）
            $stmt_update->bindValue(':account_jenda', $_POST['account_jenda'], PDO::PARAM_STR); // 性別
            $stmt_update->bindValue(':account_bloodtype', $_POST['account_bloodtype'], PDO::PARAM_STR); // 血液型
            $stmt_update->bindValue(':account_zipcord01', $_POST['account_zipcord01'], PDO::PARAM_STR); // 郵便番号（前半）
            $stmt_update->bindValue(':account_zipcord02', $_POST['account_zipcord02'], PDO::PARAM_STR); // 郵便番号（後半）
            $stmt_update->bindValue(':account_pref', $_POST['account_pref'], PDO::PARAM_STR); // 都道府県
            $stmt_update->bindValue(':account_address01', $_POST['account_address01'], PDO::PARAM_STR); // 市町村区
            $stmt_update->bindValue(':account_address02', $_POST['account_address02'], PDO::PARAM_STR); // 町名番地
            $stmt_update->bindValue(':account_address03', $_POST['account_address03'], PDO::PARAM_STR); // マンション名など
            $stmt_update->bindValue(':account_tel01', $_POST['account_tel01'], PDO::PARAM_STR); // 連絡先1（前半）
            $stmt_update->bindValue(':account_tel02', $_POST['account_tel02'], PDO::PARAM_STR); // 連絡先1（中）
            $stmt_update->bindValue(':account_tel03', $_POST['account_tel03'], PDO::PARAM_STR); // 連絡先1（後半）
            $stmt_update->bindValue(':account_tel04', $_POST['account_tel04'], PDO::PARAM_STR); // 連絡先2（前半）
            $stmt_update->bindValue(':account_tel05', $_POST['account_tel05'], PDO::PARAM_STR); // 連絡先2（中）
            $stmt_update->bindValue(':account_tel06', $_POST['account_tel06'], PDO::PARAM_STR); // 連絡先2（後半）
            $stmt_update->bindValue(':account_license_expiration_date_year', $_POST['account_license_expiration_date_year'], PDO::PARAM_STR); // 免許証有効期限（年）
            $stmt_update->bindValue(':account_license_expiration_date_month', $_POST['account_license_expiration_date_month'], PDO::PARAM_STR); // 免許証有効期限（月）
            $stmt_update->bindValue(':account_license_expiration_date_day', $_POST['account_license_expiration_date_day'], PDO::PARAM_STR); // 免許証有効期限（日）
            $stmt_update->bindValue(':account_guarentor_kana01', $_POST['account_guarentor_kana01'], PDO::PARAM_STR); // 身元保証人氏（ふりがな）
            $stmt_update->bindValue(':account_guarentor_kana02', $_POST['account_guarentor_kana02'], PDO::PARAM_STR); // 身元保証人名（ふりがな）
            $stmt_update->bindValue(':account_guarentor_name01', $_POST['account_guarentor_name01'], PDO::PARAM_STR); // 身元保証人氏（漢字）
            $stmt_update->bindValue(':account_guarentor_name02', $_POST['account_guarentor_name02'], PDO::PARAM_STR); // 身元保証人名（漢字）
            $stmt_update->bindValue(':account_relationship', $_POST['account_relationship'], PDO::PARAM_STR); // 続柄
            $stmt_update->bindValue(':account_guarentor_zipcord01', $_POST['account_guarentor_zipcord01'], PDO::PARAM_STR); // 身元保証人郵便番号（前半）
            $stmt_update->bindValue(':account_guarentor_zipcord02', $_POST['account_guarentor_zipcord02'], PDO::PARAM_STR); // 身元保証人郵便番号（後半）
            $stmt_update->bindValue(':account_guarentor_pref', $_POST['account_guarentor_pref'], PDO::PARAM_STR); // 身元保証人都道府県
            $stmt_update->bindValue(':account_guarentor_address01', $_POST['account_guarentor_address01'], PDO::PARAM_STR); // 身元保証人市町村区
            $stmt_update->bindValue(':account_guarentor_address02', $_POST['account_guarentor_address02'], PDO::PARAM_STR); // 身元保証人町名番地
            $stmt_update->bindValue(':account_guarentor_address03', $_POST['account_guarentor_address03'], PDO::PARAM_STR); // 身元保証人マンション名など
            $stmt_update->bindValue(':account_guarentor_tel01', $_POST['account_guarentor_tel01'], PDO::PARAM_STR); // 身元保証人連絡先1（前半）
            $stmt_update->bindValue(':account_guarentor_tel02', $_POST['account_guarentor_tel02'], PDO::PARAM_STR); // 身元保証人連絡先1（中）
            $stmt_update->bindValue(':account_guarentor_tel03', $_POST['account_guarentor_tel03'], PDO::PARAM_STR); // 身元保証人連絡先1（後半）
            $stmt_update->bindValue(':account_guarentor_tel04', $_POST['account_guarentor_tel04'], PDO::PARAM_STR); // 身元保証人連絡先2（前半）
            $stmt_update->bindValue(':account_guarentor_tel05', $_POST['account_guarentor_tel05'], PDO::PARAM_STR); // 身元保証人連絡先2（中）
            $stmt_update->bindValue(':account_guarentor_tel06', $_POST['account_guarentor_tel06'], PDO::PARAM_STR); // 身元保証人連絡先2（後半）
            $stmt_update->bindValue(':account_department', $_POST['account_department'], PDO::PARAM_STR); // 所属課
            $stmt_update->bindValue(':account_workclass', $_POST['account_workclass'], PDO::PARAM_STR); // 勤務区分
            $stmt_update->bindValue(':account_classification', $_POST['account_classification'], PDO::PARAM_STR); // 職種区分
            $stmt_update->bindValue(':account_enrollment', $_POST['account_enrollment'], PDO::PARAM_STR); // 在籍区分
            $stmt_update->bindValue(':account_employment_year', $_POST['account_employment_year'], PDO::PARAM_STR); // 雇用年月日（年）
            $stmt_update->bindValue(':account_employment_month', $_POST['account_employment_month'], PDO::PARAM_STR); // 雇用年月日（月）
            $stmt_update->bindValue(':account_employment_day', $_POST['account_employment_day'], PDO::PARAM_STR); // 雇用年月日（日）
            $stmt_update->bindValue(':account_appointment_year', $_POST['account_appointment_year'], PDO::PARAM_STR); // 選任年月日（年）
            $stmt_update->bindValue(':account_appointment_month', $_POST['account_appointment_month'], PDO::PARAM_STR); // 選任年月日（月）
            $stmt_update->bindValue(':account_appointment_day', $_POST['account_appointment_day'], PDO::PARAM_STR); // 選任年月日（日）
            $stmt_update->bindValue(':account_retirement_year', $_POST['account_retirement_year'], PDO::PARAM_STR); // 退職年月日（年）
            $stmt_update->bindValue(':account_retirement_month', $_POST['account_retirement_month'], PDO::PARAM_STR); // 退職年月日（月）
            $stmt_update->bindValue(':account_retirement_day', $_POST['account_retirement_day'], PDO::PARAM_STR); // 退職年月日（日）
            // $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_INT);
           // パスワードが入力されている場合のみバインドする
           if ($password_sql === ', account_password = :account_password') {
            $stmt_update->bindValue(':account_password', $hashed_password, PDO::PARAM_STR);
        }
        $stmt_update->bindValue(':account_id', $_POST['account_id'], PDO::PARAM_INT);
            
            // SQL文を実行する
            $stmt_update->execute();

            // 更新した件数を取得する
            $message = "従業員「{$_POST['account_name01']} {$_POST['account_name02']}」さんが正常に編集されました。";
            header("Location: list.php?message=" . urlencode($message));
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
                        <th>従業員No</th>
                        <td>
                            <input type="text" name="account_no" placeholder="1111" 
                                value="<?= htmlspecialchars($_POST['account_no'] ?? $account['account_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_no'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>所属営業所</th>
                        <td>
                            <select name="account_salesoffice">
                                <?= generateSelectOptions(ACCOUNT_SALESOFFICE, $account['account_salesoffice'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="たつみ" name="account_kana01" 
                                value="<?= htmlspecialchars($_POST['account_kana01'] ?? $account['account_kana01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana01'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana01'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                        <th>名（ふりがな）</th>
                        <td>
                            <input type="text" class="text" placeholder="いちばん" name="account_kana02" 
                                value="<?= htmlspecialchars($_POST['account_kana02'] ?? $account['account_kana02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_kana02'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_kana02'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>氏（漢字）</th>
                        <td>
                            <input type="text" class="text" placeholder="辰巳" name="account_name01" 
                                value="<?= htmlspecialchars($_POST['account_name01'] ?? $account['account_name01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>名（漢字）</th>
                        <td><input type="text" class="text" placeholder="一番" name="account_name02" 
                                value="<?= htmlspecialchars($_POST['account_name02'] ?? $account['account_name02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>生年月日</th>
                        <td colspan="2">
                            <select name="account_birthday_year">
                                <?php
                                    $startYear = 1939;  
                                    $endYear = date("Y") - 25;  // 現在の年から25年前を終了年に設定
                                    $selectedYear = $_POST['account_birthday_year'] ?? $account['account_birthday_year'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_birthday_month">
                                <?php
                                    $selectedMonth = $_POST['account_birthday_month'] ?? $account['account_birthday_month'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月  
                            <select name="account_birthday_day">
                                <?php
                                    $selectedDay = $_POST['account_birthday_day'] ?? $account['account_birthday_day'] ?? '';  // 登録済みの値またはPOSTされた値を使用
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>   
                    <tr>
                        <th>性別</th>
                        <td>
                            <select name="account_jenda">
                                <?= generateSelectOptions(ACCOUNT_JENDA, $_POST['account_jenda'] ?? $account['account_jenda'] ?? '') ?>
                            </select>
                        </td>
                        <th>血液型</th>
                        <td>
                            <select name="account_bloodtype">
                                <?= generateSelectOptions(ACCOUNT_BLOODTYPE, $_POST['account_bloodtype'] ?? $account['account_bloodtype'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_zipcord01" placeholder="420" 
                                value="<?= htmlspecialchars($_POST['account_zipcord01'] ?? $account['account_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_zipcord02" placeholder="0042" 
                                value="<?= htmlspecialchars($_POST['account_zipcord02'] ?? $account['account_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_zipcord'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_zipcord'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>都道府県</th>
                        <td><input type="text" class="p-region" name="account_pref" 
                                value="<?= htmlspecialchars($_POST['account_pref'] ?? $account['account_pref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <th>市町村区</th>
                        <td><input type="text" class="p-locality" name="account_address01" 
                                value="<?= htmlspecialchars($_POST['account_address01'] ?? $account['account_address01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>町名番地</th>
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
                        <th>連絡先1</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" name="account_tel01" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_tel01'] ?? $account['account_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" name="account_tel02" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel02'] ?? $account['account_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" name="account_tel03" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel03'] ?? $account['account_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_tel'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel04" size="4" maxlength="4" name="account_tel04" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_tel04'] ?? $account['account_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel05" size="4" maxlength="4" name="account_tel05" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_tel05'] ?? $account['account_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel06" size="4" maxlength="4" name="account_tel06" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_tel06'] ?? $account['account_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>免許証有効期限</th>
                        <td colspan="2">
                            <select name="account_license_expiration_date_year">
                                <?php
                                    $startYear = 2024;  
                                    $endYear = date('Y') + 10;  // 現在の年から10年後を終了年に設定(自動的に10年更新される)
                                    $selectedYear = $_POST['account_license_expiration_date_year'] ?? $account['account_license_expiration_date_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_license_expiration_date_month">
                                <?php
                                    $selectedMonth = $_POST['account_license_expiration_date_month'] ?? $account['account_license_expiration_date_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_license_expiration_date_day">
                                <?php
                                    $selectedDay = $_POST['account_license_expiration_date_day'] ?? $account['account_license_expiration_date_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
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
                            <input type="text" class="p-postal-code" size="3" maxlength="3" name="account_guarentor_zipcord01" placeholder="420" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord01'] ?? $account['account_guarentor_zipcord01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="p-postal-code" size="4" maxlength="4" name="account_guarentor_zipcord02" placeholder="0042" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_zipcord02'] ?? $account['account_guarentor_zipcord02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> 
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
                            <input type="text" class="account_guarentor_tel01" size="4" maxlength="4" name="account_guarentor_tel01" placeholder="0120" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel01'] ?? $account['account_guarentor_tel01'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel02" size="4" maxlength="4" name="account_guarentor_tel02" placeholder="1234" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel02'] ?? $account['account_guarentor_tel02'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_guarentor_tel03" size="4" maxlength="4" name="account_guarentor_tel03" placeholder="5678" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel03'] ?? $account['account_guarentor_tel03'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($errors['account_guarentor_tel'])): ?>
                                <br><span class="error"><?= htmlspecialchars($errors['account_guarentor_tel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>連絡先2</th>
                        <td colspan="2">
                            <input type="text" class="account_tel01" size="4" maxlength="4" placeholder="0000" name="account_guarentor_tel04" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel04'] ?? $account['account_guarentor_tel04'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel02" size="4" maxlength="4" placeholder="1234" name="account_guarentor_tel05" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel05'] ?? $account['account_guarentor_tel05'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> -
                            <input type="text" class="account_tel03" size="4" maxlength="4" placeholder="5678" name="account_guarentor_tel06" 
                                value="<?= htmlspecialchars($_POST['account_guarentor_tel06'] ?? $account['account_guarentor_tel06'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
                        <th>所属課</th>
                        <td>
                            <select name="account_department">
                                <?= generateSelectOptions(ACCOUNT_DEPARTMENT, $_POST['account_department'] ?? $account['account_department'] ?? '') ?>
                            </select>
                        </td>
                        <th>勤務区分</th>
                        <td>
                            <select name="account_workclass">
                                <?= generateSelectOptions(ACCOUNT_WORKCLASS, $_POST['account_workclass'] ?? $account['account_workclass'] ?? '') ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>職種区分</th>
                        <td>
                            <select name="account_classification">
                                <?= generateSelectOptions(ACCOUNT_CLASSIFICATION, $_POST['account_classification'] ?? $account['account_classification'] ?? '') ?>
                            </select>
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
                            <select name="account_employment_year">
                                <?php
                                    $startYear = 1985;  // 開始年
                                    $endYear = date('Y') + 1;  // 終了年を現在の年から1年後に設定(自動的に1年追加される)
                                    $selectedYear = $_POST['account_employment_year'] ?? $account['account_employment_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_employment_month">
                                <?php
                                    $selectedMonth = $_POST['account_employment_month'] ?? $account['account_employment_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_employment_day">
                                <?php
                                    $selectedDay = $_POST['account_employment_day'] ?? $account['account_employment_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
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
                                    $selectedYear = $_POST['account_appointment_year'] ?? $account['account_appointment_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_appointment_month">
                                <?php
                                    $selectedMonth = $_POST['account_appointment_month'] ?? $account['account_appointment_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_appointment_day">
                                <?php
                                 $selectedDay = $_POST['account_appointment_day'] ?? $account['account_appointment_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
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
                                    $endYear = date('Y') + 1;  // 現在の年から1年後までを終了年に設定(自動的に1年追加される)
                                    $selectedYear = $_POST['account_retirement_year'] ?? $account['account_retirement_year'] ?? '';
                                        echo generateYearOptions($startYear, $endYear, $selectedYear);
                                ?>
                            </select>年
                            <select name="account_retirement_month">
                                <?php
                                    $selectedMonth = $_POST['account_retirement_month'] ?? $account['account_retirement_month'] ?? '';
                                        echo generateMonthOptions($selectedMonth);
                                ?>
                            </select>月
                            <select name="account_retirement_day">
                                <?php
                                    $selectedDay = $_POST['account_retirement_day'] ?? $account['account_retirement_day'] ?? '';
                                        echo generateDayOptions($selectedDay);
                                ?>
                            </select>日
                        </td>
                    </tr>                        
                </table>
            </div>    

            <!-- <div table class="flex">
                <input type="submit" name="submit" value="更新">
            </div> -->
            <!-- ログインユーザーの勤務区分が「管理者」または「役員」の場合のみ、更新ボタンを表示 -->
            <?php if (isset($logged_in_workclass) && ($logged_in_workclass === 1 || $logged_in_workclass === 2)): ?>
                <div class="flex">
                    <input type="submit" name="submit" value="更新">
                </div>
            <?php endif; ?>

        </form>

    </body>
    
</html>
