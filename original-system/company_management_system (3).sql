-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-09-25 19:31:05
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `company_management_system`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `account_password` varchar(100) DEFAULT NULL COMMENT 'パスワード',
  `account_no` int(5) NOT NULL COMMENT '従業員No',
  `account_salesoffice` int(1) NOT NULL COMMENT '所属営業所',
  `account_kana01` varchar(20) NOT NULL COMMENT 'なまえ（氏）',
  `account_kana02` varchar(20) NOT NULL COMMENT 'なまえ（名）',
  `account_name01` varchar(20) NOT NULL COMMENT '漢字（氏）',
  `account_name02` varchar(20) NOT NULL COMMENT '漢字（名）',
  `account_birthday` date DEFAULT NULL COMMENT '生年月日',
  `account_jenda` int(1) NOT NULL COMMENT '性別',
  `account_bloodtype` int(1) NOT NULL COMMENT '血液型',
  `account_zipcord` char(8) DEFAULT NULL COMMENT '郵便番号',
  `account_pref` varchar(4) NOT NULL COMMENT '都道府県',
  `account_address01` varchar(150) NOT NULL COMMENT '市町村区',
  `account_address02` varchar(150) NOT NULL COMMENT '町名番地',
  `account_address03` varchar(100) DEFAULT NULL COMMENT 'マンション名など',
  `account_tel1` varchar(13) DEFAULT NULL COMMENT '連絡先1',
  `account_tel2` varchar(13) DEFAULT NULL COMMENT '連絡先2',
  `account_license_expiration_date` date DEFAULT NULL COMMENT '免許証有効期限',
  `account_guarentor_kana01` varchar(20) DEFAULT NULL COMMENT '保証人氏（ふりがな）',
  `account_guarentor_kana02` varchar(20) DEFAULT NULL COMMENT '保証人名（ふりがな）',
  `account_guarentor_name01` varchar(20) DEFAULT NULL COMMENT '保証人氏（漢字）',
  `account_guarentor_name02` varchar(20) DEFAULT NULL COMMENT '保証人名（漢字）',
  `account_relationship` varchar(5) DEFAULT NULL COMMENT '続柄',
  `account_guarentor_zipcode` char(8) DEFAULT NULL COMMENT '保証人郵便番号',
  `account_guarentor_pref` varchar(4) DEFAULT NULL COMMENT '保証人都道府県',
  `account_guarentor_address01` varchar(150) DEFAULT NULL COMMENT '保証人市区町村',
  `account_guarentor_address02` varchar(150) DEFAULT NULL COMMENT '保証人町名番地',
  `account_guarentor_address03` varchar(100) DEFAULT NULL COMMENT '保証人マンション名など',
  `account_guarentor_tel1` varchar(13) DEFAULT NULL COMMENT '保証人連絡先1',
  `account_guarentor_tel2` varchar(13) DEFAULT NULL COMMENT '保証人連絡先2',
  `account_department` int(1) NOT NULL COMMENT '所属課',
  `account_workclass` int(2) NOT NULL COMMENT '勤務区分',
  `account_classification` int(1) NOT NULL COMMENT '職種区分',
  `account_enrollment` int(1) DEFAULT NULL COMMENT '在籍区分',
  `account_employment_date` date DEFAULT NULL COMMENT '雇用年月日',
  `account_appointment_date` date DEFAULT NULL COMMENT '選任年月日',
  `account_retirement_date` date DEFAULT NULL COMMENT '退職年月日',
  `registration_date` date NOT NULL COMMENT '登録日',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '更新日',
  `last_login_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_password`, `account_no`, `account_salesoffice`, `account_kana01`, `account_kana02`, `account_name01`, `account_name02`, `account_birthday`, `account_jenda`, `account_bloodtype`, `account_zipcord`, `account_pref`, `account_address01`, `account_address02`, `account_address03`, `account_tel1`, `account_tel2`, `account_license_expiration_date`, `account_guarentor_kana01`, `account_guarentor_kana02`, `account_guarentor_name01`, `account_guarentor_name02`, `account_relationship`, `account_guarentor_zipcode`, `account_guarentor_pref`, `account_guarentor_address01`, `account_guarentor_address02`, `account_guarentor_address03`, `account_guarentor_tel1`, `account_guarentor_tel2`, `account_department`, `account_workclass`, `account_classification`, `account_enrollment`, `account_employment_date`, `account_appointment_date`, `account_retirement_date`, `registration_date`, `updated_at`, `last_login_time`) VALUES
(23, '$2y$10$yfrcC9SPl9tNxZzYjWcPoudhNzQt5jFuwnaY.zZ.20hBI0/b54mUe', 1, 1, 'まつした', 'ひさお', '松下', '壽夫', '1983-03-27', 1, 2, '420-0945', '静岡県', '静岡市葵区', '桜町2丁目6-92', '向島方', '080-5127-1303', '054-254-4641', '2024-09-27', 'まつした', 'はつえ', '松下', '初恵', '母', '420-0945', '静岡県', '静岡市葵区', '桜町2-6-92', '', '054-054-055', '0055-0540-054', 1, 2, 1, 1, '2019-07-20', '2019-07-21', '0000-00-00', '2024-09-06', '2024-09-06 11:40:31', '2024-09-25 19:26:13'),
(24, '$2y$10$Ptta5DCL0XNflI/QY1anae8J3H8q4qcT4t/JBTZvlZ6UGe0zaaE/m', 2, 1, 'しらい', 'ゆき', '白井', '佑紀', '1979-04-05', 2, 1, '420-0042', '静岡県', '静岡市葵区', '駒形通2-2-25', '辰巳マンション1112S', '050-5605-0565', '0506-0506-056', '2024-09-22', 'たつみ', 'おやじ', '辰巳', '親父', '父', '420-0042', '静岡県', '静岡市葵区', '駒形通2丁目2-25', '辰巳マンション101A', '0540-0560-546', '0556-5056-056', 1, 5, 1, 1, '2022-09-06', '2022-09-06', '0000-00-00', '2024-09-06', '2024-09-06 11:43:00', '2024-09-22 02:40:14');

-- --------------------------------------------------------

--
-- テーブルの構造 `vehicles`
--

CREATE TABLE `vehicles` (
  `car_id` int(11) NOT NULL,
  `car_number_name` varchar(5) NOT NULL COMMENT '車番',
  `car_model` varchar(10) NOT NULL COMMENT '車種',
  `car_name` varchar(30) NOT NULL COMMENT '車名',
  `car_transpottaition` varchar(5) NOT NULL COMMENT '運輸支局',
  `car_classification_no` int(3) NOT NULL COMMENT '分類番号',
  `car_purpose` varchar(1) NOT NULL COMMENT '用途別',
  `car_number` varchar(5) NOT NULL COMMENT '番号',
  `car_chassis_number` varchar(20) NOT NULL COMMENT '車台番号',
  `first_registration_day` date NOT NULL COMMENT '初年度登録年月',
  `vehicle_inspection_day` date NOT NULL COMMENT '車検有効期限',
  `compulsory_automobile_day` date NOT NULL COMMENT '自賠責有効期限',
  `meter_inspection_day` date DEFAULT NULL COMMENT 'メーター検査',
  `lp_gas_day` date DEFAULT NULL COMMENT 'LPガス容器有効',
  `owner_name` varchar(20) NOT NULL COMMENT '所有者（名）',
  `owner_address` varchar(150) NOT NULL COMMENT '所有者住所',
  `user_name` varchar(20) NOT NULL COMMENT '使用者（名）',
  `user_address` varchar(150) NOT NULL COMMENT '使用者（住所）',
  `headquarters_address` int(1) NOT NULL COMMENT '本拠の位置',
  `vehicle_registrationday` datetime NOT NULL COMMENT '登録日',
  `vehicle_updateday` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新日',
  `is_suspended` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `vehicles`
--

INSERT INTO `vehicles` (`car_id`, `car_number_name`, `car_model`, `car_name`, `car_transpottaition`, `car_classification_no`, `car_purpose`, `car_number`, `car_chassis_number`, `first_registration_day`, `vehicle_inspection_day`, `compulsory_automobile_day`, `meter_inspection_day`, `lp_gas_day`, `owner_name`, `owner_address`, `user_name`, `user_address`, `headquarters_address`, `vehicle_registrationday`, `vehicle_updateday`, `is_suspended`) VALUES
(19, '113', 'コンフォート', 'トヨタ', '静岡', 500, 'あ', '29-69', 'TSS11-9012867', '2012-03-01', '2024-09-29', '2025-03-28', '2024-10-01', NULL, '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', 2, '2024-09-18 23:31:58', '2024-09-21 07:37:18', 0),
(20, '114', 'シエンタ', 'トヨタ', '静岡', 500, 'あ', '36-15', 'NHP170-7131997', '2018-06-01', '2024-09-25', '2025-06-24', '2024-12-01', NULL, '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', 3, '2024-09-19 10:20:34', '2024-09-21 07:48:02', 0),
(21, '115', 'コンフォート', 'トヨタ', '静岡', 500, 'あ', '32-12', 'TSS11-9023953', '2014-08-01', '2025-08-27', '2025-08-26', NULL, NULL, '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', 3, '2024-09-19 10:22:42', '2024-09-19 01:22:42', 0),
(22, '200', 'コンフォート', 'トヨタ', '静岡', 500, 'あ', '26-69', 'NHP170-7131997', '1999-01-01', '2021-01-03', '2025-01-16', '2024-10-01', '2023-12-01', '辰巳タクシー株式会社', '静岡県静岡市駿河区弥生町2-74', '辰巳タクシー株式会社', '静岡県静岡市葵区駒形通2丁目2-25', 3, '2024-09-19 23:58:12', '2024-09-19 15:23:04', 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- テーブルのインデックス `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`car_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- テーブルの AUTO_INCREMENT `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
