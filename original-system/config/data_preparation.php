<?php
// accountsのデータを取得していると仮定します
$accounts = []; // ここにDBから取得したデータが入ると仮定

foreach ($accounts as &$account) {
    // 登録・更新の日付のフォーマット
    $account['registration_date_formatted'] = isset($account['registration_date']) ? (new DateTime($account['registration_date']))->format('Y年m月d日') : '日付なし';
    $account['updated_at_formatted'] = isset($account['updated_at']) ? (new DateTime($account['updated_at']))->format('Y年m月d日') : '日付なし';

    // 免許証有効期限を「YYYY年MM月DD日」形式にフォーマット
    $account['account_license_expiration_date_formatted'] = htmlspecialchars($account['account_license_expiration_date_year'] . '年' . $account['account_license_expiration_date_month'] . '月' . $account['account_license_expiration_date_day'] . '日', ENT_QUOTES, 'UTF-8');

    // 生年月日を「YYYY年MM月DD日」形式にフォーマット
    $account['account_birthday_formatted'] = htmlspecialchars($account['account_birthday_year'] . '年' . $account['account_birthday_month'] . '月' . $account['account_birthday_day'] . '日', ENT_QUOTES, 'UTF-8');

    // 年齢を計算
    $birthDate = $account['account_birthday_year'] . '-' . str_pad($account['account_birthday_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($account['account_birthday_day'], 2, '0', STR_PAD_LEFT);
    $birthDateTime = new DateTime($birthDate);
    $currentDateTime = new DateTime();
    $ageInterval = $currentDateTime->diff($birthDateTime);
    $account['age_years'] = $ageInterval->y;
    $account['age_months'] = $ageInterval->m;

    // 雇用年月日を「YYYY年MM月DD日」形式にフォーマット
    $account['account_employmentday_formatted'] = htmlspecialchars($account['account_employment_year'] . '年' . $account['account_employment_month'] . '月' . $account['account_employment_day'] . '日', ENT_QUOTES, 'UTF-8');

    // 勤続年数を計算
    $employmentDate = $account['account_employment_year'] . '-' . str_pad($account['account_employment_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($account['account_employment_day'], 2, '0', STR_PAD_LEFT);
    $employmentDateTime = new DateTime($employmentDate);
    $serviceInterval = $currentDateTime->diff($employmentDateTime);
    $account['service_years'] = $serviceInterval->y;
    $account['service_months'] = $serviceInterval->m;

    // データのエスケープ
    $account['account_no_escaped'] = htmlspecialchars($account['account_no'], ENT_QUOTES, 'UTF-8');
    $account['account_kana_escaped'] = htmlspecialchars($account['account_kana01'] . '　' . $account['account_kana02'], ENT_QUOTES, 'UTF-8');
    $account['account_name_escaped'] = htmlspecialchars($account['account_name01'] . '　' . $account['account_name02'], ENT_QUOTES, 'UTF-8');

    // 定義済みの定数を使用して表示用の値を設定
    $account['account_department_name'] = ACCOUNT_DEPARTMENT[$account['account_department']];
    $account['account_classification_name'] = ACCOUNT_CLASSIFICATION[$account['account_classification']];
    $account['account_workclass_name'] = ACCOUNT_WORKCLASS[$account['account_workclass']];
}

unset($account); // 参照をクリア
?>