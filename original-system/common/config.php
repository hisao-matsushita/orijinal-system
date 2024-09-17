<?php
// データベース設定
// $dsnAccount = 'mysql:dbname=company_management_system;host=localhost;charset=utf8mb4';
// $userAccount = 'root';
// $passwordAccount = '';

// データベース設定
$dsnAccount = 'mysql:dbname=tatsumi-taxi_management_db;host=mysql57.tatsumi-taxi.sakura.ne.jp;charset=utf8mb4';
$userAccount = 'tatsumi-taxi';
$passwordAccount = 'h-03271303';

// PDOインスタンスを取得する関数
function getPdoInstance($dsn, $user, $password) {
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        exit("データベース接続エラー: " . $e->getMessage());
    }
}

// PDOインスタンス
$pdoAccount = getPdoInstance($dsnAccount, $userAccount, $passwordAccount);

// 共通定数の設定
const ACCOUNT_SALESOFFICE = [1 => '本社営業所', 2 => '向敷地営業所'];
const ACCOUNT_JENDA = [1 => '男', 2 => '女'];
const ACCOUNT_BLOODTYPE = [1 => 'A型', 2 => 'B型', 3 => 'O型', 4 => 'AB型'];
const ACCOUNT_DEPARTMENT = [1 => '内勤', 2 => '外勤'];
const ACCOUNT_WORKCLASS = [
    1 => '役員', 2 => '管理者', 3 => '事務員', 4 => '整備士', 5 => '配車係',
    6 => '乗務A', 7 => '乗務B', 8 => '乗務C', 9 => '乗務D', 10 => '乗務E',
    11 => '乗務F', 12 => '乗務G', 13 => '乗務H'
];
const ACCOUNT_CLASSIFICATION = [1 => '正社員', 2 => '準社員', 3 => '嘱託'];
const ACCOUNT_ENROLLMENT = [1 => '本採用', 2 => '中途採用', 3 => '退職'];
const HEADQUARTERS_ADDRESS = [
    1 => '静岡県静岡市葵区駒形通2丁目2-25',
    2 => '静岡県静岡市駿河区宮本町8-17',
    3 => '静岡県静岡市駿河区向敷地4丁目10-70'
];


//configに書くのは間違い。分かりずらい、見ずらい
// 年、月、日の選択肢生成関数
function generateYearOptions($startYear, $endYear, $selectedValue = '') {
    $years = [];
    for ($year = $startYear; $year <= $endYear; $year++) {
        $years[$year] = $year;
    }
    echo generateSelectOptions($years, $selectedValue);
}

function generateMonthOptions($selectedValue = '') {
    $months = [];
    for ($month = 1; $month <= 12; $month++) {
        $months[$month] = $month;
    }
    echo generateSelectOptions($months, $selectedValue);
}

function generateDayOptions($selectedValue = '') {
    $days = [];
    for ($day = 1; $day <= 31; $day++) {
        $days[$day] = $day;
    }
    echo generateSelectOptions($days, $selectedValue);
}

function generateSelectOptions($optionsArray, $selectedValue = '') {
    $html = '<option value="">選択</option>';
    foreach ($optionsArray as $value => $label) {
        $isSelected = ($value == $selectedValue) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $isSelected . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    return $html;
}

?>