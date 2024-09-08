<?php
// 必須項目のバリデーション関数
function validateRequiredFields($fields) {
    global $errors;

    foreach ($fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[$field] = $label . 'は必須です。';
        }
    }
}

// 車両番号のバリデーション関数
function validateVehicleNumber() {
    global $errors;

    // 車両番号に関する全てのフィールドが埋まっているか確認
    if (empty($_POST['car_transpottaition']) || 
        empty($_POST['car_classification_no']) || 
        empty($_POST['car_purpose']) || 
        empty($_POST['car_number01']) || 
        empty($_POST['car_number02'])) {

        $errors['vehicle_number'] = '車両番号は必須です。'; // 統一されたエラーメッセージ
    }
}

// 半角数字のチェック関数
function validateNumericFields() {
    global $errors;

    if (!empty($_POST['car_classification_no']) && !preg_match('/^\d+$/', $_POST['car_classification_no'])) {
        $errors['car_classification_no'] = '分類番号は半角数字で入力してください。';
    }
    if (!empty($_POST['car_number01']) && !preg_match('/^\d+$/', $_POST['car_number01'])) {
        $errors['car_number01'] = '車番1は半角数字で入力してください。';
    }
    if (!empty($_POST['car_number02']) && !preg_match('/^\d+$/', $_POST['car_number02'])) {
        $errors['car_number02'] = '車番2は半角数字で入力してください。';
    }
}

// すべてのバリデーションを一括で処理する関数
function validateAllVehicleData($pdoVehicles, $isUpdate = false) {  // $isUpdateフラグを追加
    // 必須項目のバリデーション
    validateRequiredFields([
        'car_model' => '車種',
        'car_name' => '車名'
    ]);

    // 車両番号のバリデーション（新規登録時のみ重複チェック）
    validateCarNumber($_POST['car_number_name'], $pdoVehicles, $isUpdate);

    // 半角数字のバリデーション
    validateNumericFields();

    // 車台番号のバリデーション
    validateCarChassisNumber(); 

    // 初年度登録年月のバリデーション
    validateFirstRegistrationDate();  

    // 自賠責有効期限のバリデーション
    validateCompulsoryAutomobileDate();

    // 所有者・使用者情報のバリデーション
    validateOwnerAndUserInfo(); 
}

// 車番のバリデーション（新規登録時のみ重複チェック）
function validateCarNumber($car_number_name, $pdoVehicles, $isUpdate = false) {  // $isUpdateフラグを追加
    global $errors;

    if (preg_match('/[０-９]/u', $car_number_name)) {  // 全角数字が含まれていないかチェック
        $errors['car_number_name'] = '車番の数字は半角で入力してください。';
        return;
    }

    // 新規登録時にのみ重複チェックを行う
    if (!$isUpdate) {  // $isUpdateがfalseの場合、新規登録のため重複チェック
        $stmt = $pdoVehicles->prepare('SELECT COUNT(*) FROM vehicles WHERE car_number_name = :car_number_name');
        $stmt->bindValue(':car_number_name', $car_number_name, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // 重複がある場合はエラーメッセージ
        if ($count > 0) {
            $errors['car_number_name'] = 'この車番は既に登録されています。';
        }
    }
}

// 車台番号のバリデーション関数
function validateCarChassisNumber() {
    global $errors;

    // 必須チェック
    if (empty($_POST['car_chassis_number'])) {
        $errors['car_chassis_number'] = '車台番号は必須です。';
    } 
    // 半角英字、半角数字、半角記号のみを許可する正規表現
    elseif (!preg_match('/^[A-Za-z0-9\-]+$/', $_POST['car_chassis_number'])) {
        $errors['car_chassis_number'] = '車台番号は半角英字、半角数字、半角記号（-）のみで入力してください。';
    }
}

// 初年度登録年月のバリデーション関数
function validateFirstRegistrationDate() {
    global $errors;

    // 年と月のどちらかが選択されていない場合
    if (empty($_POST['first_registration_year']) || empty($_POST['first_registration_month'])) {
        $errors['first_registration_date'] = '必須項目です。選択してください。';
    }
}

// 車検有効期限のバリデーション関数
function validateVehicleInspectionDate() {
    global $errors;

    // 年、月、日がすべて選択されているかを確認
    if (empty($_POST['vehicle_inspection_year']) || empty($_POST['vehicle_inspection_month']) || empty($_POST['vehicle_inspection_day'])) {
        $errors['vehicle_inspection_date'] = '必須項目です。選択してください。';
    }
}

// 自賠責有効期限のバリデーション関数
function validateCompulsoryAutomobileDate() {
    global $errors;

    // 年、月、日がすべて選択されているかを確認
    if (empty($_POST['compulsory_automobile_year']) || empty($_POST['compulsory_automobile_month']) || empty($_POST['compulsory_automobile_day'])) {
        $errors['compulsory_automobile_date'] = '必須項目です。選択してください。';
    }
}

function validateOwnerAndUserInfo() {
    global $errors;

    // 所有者の氏名
    if (empty($_POST['owner_name'])) {
        $errors['owner_name'] = '所有者の氏名または名称は必須です。';
    }

    // 所有者の住所
    if (empty($_POST['owner_address'])) {
        $errors['owner_address'] = '所有者の住所は必須です。';
    }

    // 使用者の氏名
    if (empty($_POST['user_name'])) {
        $errors['user_name'] = '使用者の氏名または名称は必須です。';
    }

    // 使用者の住所
    if (empty($_POST['user_address'])) {
        $errors['user_address'] = '使用者の住所は必須です。';
    }

    // 使用本拠地の位置
    if (empty($_POST['headquarters_address'])) {
        $errors['headquarters_address'] = '使用本拠地の位置は必須です。';
    }
}
?>