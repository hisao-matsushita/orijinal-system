<?php
// バリデーションパターンの定義
$patterns = [
    'half_width_numeric' => '/^\d+$/',
    'hiragana' => '/^[ぁ-んー　]+$/u',
    'password' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
    'address_kanji_hiragana_english' => '/^[\p{Han}\p{Hiragana}A-Z\d!@#$%^&*()\-_=+{};:,<.>]+$/u',
    'address_kanji_hiragana_katakana_english' => '/^[\p{Han}\p{Hiragana}\p{Katakana}A-Za-z0-9!@#$%^&*()\-_=+{};:,<.>]+$/u',
];

function validate($input, $pattern, $errorMessage, &$errors, $fieldName) {
    if (!empty($input) && !preg_match($pattern, $input)) {
        $errors[$fieldName] = $errorMessage;
    }
}

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

function performValidation(&$errors, $pdo, $patterns) {

    validate($_POST['account_no'] ?? '', $patterns['half_width_numeric'], '半角数字のみで入力してください。', $errors, 'account_no');
    validate($_POST['account_kana01'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana01');
    validate($_POST['account_kana02'] ?? '', $patterns['hiragana'], 'ひらがなのみ入力してください。', $errors, 'account_kana02');

    // 必須項目のバリデーション
    if (empty($_POST['account_no'])) {
        $errors['account_no'] = '従業員Noは必須です。';
    }
    if (empty($_POST['account_salesoffice'])) {
        $errors['account_salesoffice'] = '所属営業所は必須です。';
    }
    if (empty($_POST['account_kana01'])) {
        $errors['account_kana01'] = '氏（ふりがな）は必須です。';
    }
    if (empty($_POST['account_kana02'])) {
        $errors['account_kana02'] = '名（ふりがな）は必須です。';
    }
    if (empty($_POST['account_name01'])) {
        $errors['account_name01'] = '氏（漢字）は必須です。';
    }
    if (empty($_POST['account_name02'])) {
        $errors['account_name02'] = '名（漢字）は必須です。';
    }
    if (empty($_POST['account_birthday_year']) || empty($_POST['account_birthday_month']) || empty($_POST['account_birthday_day'])) {
        $errors['account_birthday'] = '生年月日は必須です。';
    }
    if (empty($_POST['account_jenda'])) {
        $errors['account_jenda'] = '性別は必須です。';
    }
    if (empty($_POST['account_bloodtype'])) {
        $errors['account_bloodtype'] = '血液型は必須です。';
    }
    if (empty($_POST['account_zipcord01']) || empty($_POST['account_zipcord02'])) {
        $errors['account_zipcord'] = '郵便番号は必須です。';
    }
    if (empty($_POST['account_pref'])) {
        $errors['account_pref'] = '都道府県は必須です。';
    }
    if (empty($_POST['account_address01'])) {
        $errors['account_address01'] = '市町村区は必須です。';
    }
    if (empty($_POST['account_address02'])) {
        $errors['account_address02'] = '町名番地は必須です。';
    }
    if (empty($_POST['account_tel01']) || empty($_POST['account_tel02']) || empty($_POST['account_tel03'])) {
        $errors['account_tel'] = '連絡先1は必須です。';
    }
    if (empty($_POST['account_license_expiration_date_year']) || empty($_POST['account_license_expiration_date_month']) || empty($_POST['account_license_expiration_date_day'])) {
        $errors['account_license_expiration_date'] = '免許証有効期限は必須です。';
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

    return $errors;
}
?>