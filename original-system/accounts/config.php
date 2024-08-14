<?php
$dsn = 'mysql:dbname=php_account_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

const ACCOUNT_SALESOFFICE = [1 =>'本社営業所', 2 =>'向敷地営業所'];
const ACCOUNT_JENDA = [1 =>'男', 2 =>'女'];
const ACCOUNT_BLOODTYPE = [1 =>'A型', 2 =>'B型', 3 =>'O型', 4 =>'AB型'];
const ACCOUNT_DEPARTMENT = [1 =>'内勤', 2 =>'外勤'];
const ACCOUNT_WORKCLASS = [1 =>'役員', 2 =>'管理者', 3 => '事務員', 4 =>'整備士', 5 =>'配車係', 6 =>'乗務A', 7 =>'乗務B', 8 =>'乗務C', 9 =>'乗務D',
                           10 =>'乗務E', 11 =>'乗務F', 12 =>'乗務G', 13 =>'乗務H'];
const ACCOUNT_CLASSIFICATION = [1 =>'正社員', 2 =>'準社員', 3 => '嘱託'];                         
const ACCOUNT_ENROLLMENT = [1 => '本採用', 2 => '中途採用', 3 => '退職'];

// 年の選択肢を生成する関数
function generateYearOptions($startYear, $endYear, $selectedValue = '') {
    $years = [];
    for ($year = $startYear; $year <= $endYear; $year++) {
        $years[$year] = $year;
    }
    return generateSelectOptions($years, $selectedValue);
}
// 月の選択肢を生成する関数
function generateMonthOptions($selectedValue = '') {
    $months = [];
    for ($month = 1; $month <= 12; $month++) {
        $months[$month] = $month;
    }
    return generateSelectOptions($months, $selectedValue);
}
// 日の選択肢を生成する関数
function generateDayOptions($selectedValue = '') {
    $days = [];
    for ($day = 1; $day <= 31; $day++) {
        $days[$day] = $day;
    }
    return generateSelectOptions($days, $selectedValue);
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