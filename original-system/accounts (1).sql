-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-08-29 15:58:09
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
-- データベース: `php_account_app`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL COMMENT '従業員ID',
  `account_password` varchar(100) DEFAULT NULL COMMENT 'パスワード',
  `account_no` int(5) NOT NULL COMMENT '従業員No',
  `account_salesoffice` int(1) NOT NULL COMMENT '所属営業所',
  `account_kana01` varchar(20) NOT NULL COMMENT 'なまえ（氏）',
  `account_kana02` varchar(20) NOT NULL COMMENT 'なまえ（名）',
  `account_name01` varchar(20) NOT NULL COMMENT '漢字（氏）',
  `account_name02` varchar(20) NOT NULL COMMENT '漢字（名）',
  `account_birthday_year` year(4) NOT NULL COMMENT '誕生日（年）',
  `account_birthday_month` int(2) NOT NULL,
  `account_birthday_day` int(2) NOT NULL,
  `account_jenda` int(1) NOT NULL COMMENT '性別',
  `account_bloodtype` int(1) NOT NULL COMMENT '血液型',
  `account_zipcord01` int(3) DEFAULT NULL COMMENT '郵便番号上3桁',
  `account_zipcord02` int(4) DEFAULT NULL COMMENT '郵便番号下4桁',
  `account_pref` varchar(4) NOT NULL COMMENT '都道府県',
  `account_address01` varchar(150) NOT NULL COMMENT '市町村区',
  `account_address02` varchar(150) NOT NULL COMMENT '町名番地',
  `account_address03` varchar(100) DEFAULT NULL COMMENT 'マンション名など',
  `account_tel01` int(4) NOT NULL COMMENT '電話番号（上）',
  `account_tel02` int(4) NOT NULL COMMENT '電話番号（中）',
  `account_tel03` int(4) NOT NULL COMMENT '電話番号（下）',
  `account_tel04` int(4) DEFAULT NULL COMMENT '電話番号2（上）',
  `account_tel05` int(4) DEFAULT NULL COMMENT '電話番号2（中）',
  `account_tel06` int(4) DEFAULT NULL COMMENT '電話番号2（下）',
  `account_license_expiration_date_year` year(4) NOT NULL COMMENT '免許証有効期限（年）',
  `account_license_expiration_date_month` int(2) NOT NULL,
  `account_license_expiration_date_day` int(2) NOT NULL,
  `account_guarentor_kana01` varchar(20) DEFAULT NULL COMMENT '保証人氏（ふりがな）',
  `account_guarentor_kana02` varchar(20) DEFAULT NULL COMMENT '保証人名（ひらがな）',
  `account_guarentor_name01` varchar(20) DEFAULT NULL COMMENT '保証人氏（漢字）',
  `account_guarentor_name02` varchar(20) DEFAULT NULL COMMENT '保証人名（漢字）',
  `account_relationship` varchar(5) DEFAULT NULL COMMENT '続柄',
  `account_guarentor_zipcord01` int(3) DEFAULT NULL COMMENT '保証人郵便番号（上）',
  `account_guarentor_zipcord02` int(4) DEFAULT NULL COMMENT '保証人郵便番号（下）',
  `account_guarentor_pref` varchar(4) DEFAULT NULL COMMENT '保証人都道府県',
  `account_guarentor_address01` varchar(150) DEFAULT NULL COMMENT '保証人市区町村',
  `account_guarentor_address02` varchar(150) DEFAULT NULL COMMENT '保証人町名番地',
  `account_guarentor_address03` varchar(100) DEFAULT NULL COMMENT 'マンション名など',
  `account_guarentor_tel01` int(4) DEFAULT NULL COMMENT '保証人電話番号（上）',
  `account_guarentor_tel02` int(4) DEFAULT NULL COMMENT '保証人電話番号（中）',
  `account_guarentor_tel03` int(4) DEFAULT NULL COMMENT '保証人電話番号（下）',
  `account_guarentor_tel04` int(4) DEFAULT NULL COMMENT '保証人電話番号2（上）',
  `account_guarentor_tel05` int(4) DEFAULT NULL COMMENT '保証人電話番号2（中）',
  `account_guarentor_tel06` int(4) DEFAULT NULL COMMENT '保証人電話番号2（下）',
  `account_department` int(1) NOT NULL COMMENT '所属課',
  `account_classification` int(1) NOT NULL COMMENT '職種区分',
  `account_workclass` int(2) NOT NULL COMMENT '勤務区分',
  `account_enrollment` int(1) DEFAULT NULL COMMENT '在籍区分',
  `account_employment_year` year(4) NOT NULL COMMENT '雇用年月日（年）',
  `account_employment_month` int(2) NOT NULL,
  `account_employment_day` int(2) NOT NULL,
  `account_appointment_year` year(4) NOT NULL COMMENT '選任年月日（年）',
  `account_appointment_month` int(2) NOT NULL,
  `account_appointment_day` int(2) NOT NULL,
  `account_retirement_year` year(4) DEFAULT NULL COMMENT '退職年月日（年）',
  `account_retirement_month` int(2) DEFAULT NULL,
  `account_retirement_day` int(2) DEFAULT NULL,
  `registration_date` date NOT NULL DEFAULT curdate(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_password`, `account_no`, `account_salesoffice`, `account_kana01`, `account_kana02`, `account_name01`, `account_name02`, `account_birthday_year`, `account_birthday_month`, `account_birthday_day`, `account_jenda`, `account_bloodtype`, `account_zipcord01`, `account_zipcord02`, `account_pref`, `account_address01`, `account_address02`, `account_address03`, `account_tel01`, `account_tel02`, `account_tel03`, `account_tel04`, `account_tel05`, `account_tel06`, `account_license_expiration_date_year`, `account_license_expiration_date_month`, `account_license_expiration_date_day`, `account_guarentor_kana01`, `account_guarentor_kana02`, `account_guarentor_name01`, `account_guarentor_name02`, `account_relationship`, `account_guarentor_zipcord01`, `account_guarentor_zipcord02`, `account_guarentor_pref`, `account_guarentor_address01`, `account_guarentor_address02`, `account_guarentor_address03`, `account_guarentor_tel01`, `account_guarentor_tel02`, `account_guarentor_tel03`, `account_guarentor_tel04`, `account_guarentor_tel05`, `account_guarentor_tel06`, `account_department`, `account_classification`, `account_workclass`, `account_enrollment`, `account_employment_year`, `account_employment_month`, `account_employment_day`, `account_appointment_year`, `account_appointment_month`, `account_appointment_day`, `account_retirement_year`, `account_retirement_month`, `account_retirement_day`, `registration_date`, `updated_at`) VALUES
(169, '$2y$10$9cbbWfEsZmMjZ.MnGeSXseeqK9u1NfbNysi1UaCI8F8QScicSY4Z.', 1, 1, 'まつした', 'ひさお', '松下', '壽夫', '1983', 3, 27, 1, 1, 420, 945, '静岡県', '静岡市葵区', '桜町2丁目6-92', '向島方', 45, 54, 1, 0, 0, 0, '2024', 8, 31, 'たつみ', 'おやじ', '辰巳', '親父', '父', 420, 42, '静岡県', '静岡市葵区', '駒形通2丁目2-25', '辰巳マンションA1025', 54, 254, 2471, 54, 254, 4641, 1, 1, 2, 1, '2019', 7, 20, '2019', 7, 21, '0000', 0, 0, '2024-08-23', '2024-08-29 00:47:59'),
(170, '$2y$10$HARNyi5d5U6Qy23UUL4WNuSpNaML31fHZEjUN1C1iHp2AoKdTg1/C', 2, 1, 'たつみ', 'いちばん', '辰巳', '一番', '1949', 10, 15, 1, 1, 420, 42, '静岡県', '静岡市葵区', '駒形通', '', 54, 254, 120, 120, 123, 126, '2033', 11, 17, '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 1, 3, 0, '2024', 8, 23, '2024', 8, 23, '0000', 0, 0, '2024-08-23', '2024-08-23 00:25:36'),
(171, NULL, 3, 1, 'まつした', 'ひさお', '松下', '壽夫', '1939', 1, 1, 1, 1, 420, 42, '静岡県', '静岡市葵区', '駒形通', '', 54, 54, 54, 0, 0, 0, '2024', 1, 17, '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 1, 11, 0, '2024', 8, 23, '2024', 8, 23, '0000', 0, 0, '2024-08-23', '2024-08-28 02:02:50'),
(172, NULL, 4, 1, 'まつした', 'ひさお', '白井', 'テスト', '1939', 1, 1, 1, 1, 420, 42, '静岡県', '静岡市葵区', '駒形通', NULL, 45, 54, 54, NULL, NULL, NULL, '2033', 11, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 11, NULL, '2024', 8, 23, '2024', 8, 23, NULL, NULL, NULL, '2024-08-23', '2024-08-23 00:47:13'),
(173, NULL, 5, 2, 'まつした', 'ひさお', '白井', '壽夫', '1940', 2, 2, 1, 1, 420, 42, '静岡県', '静岡市葵区', '駒形通', NULL, 54, 54, 54, NULL, NULL, NULL, '2034', 11, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 12, NULL, '2024', 8, 23, '2024', 8, 23, NULL, NULL, NULL, '2024-08-23', '2024-08-23 00:51:27'),
(174, NULL, 6, 1, 'まつした', 'てすと', '松下', 'テスト', '1958', 1, 19, 1, 2, 420, 42, '静岡県', '静岡市葵区', '駒形通', '', 6, 54, 0, 0, 0, 0, '2034', 11, 22, '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 1, 12, 0, '2024', 8, 23, '2024', 8, 23, '0000', 0, 0, '2024-08-23', '2024-08-23 11:17:14'),
(175, NULL, 300, 1, 'しらい', 'いちばん', '白井', 'テスト', '1941', 3, 3, 2, 2, 420, 41, '静岡県', '静岡市葵区', '双葉町', NULL, 260, 620, 620, NULL, NULL, NULL, '2025', 11, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 11, NULL, '2024', 8, 25, '2024', 8, 25, NULL, NULL, NULL, '2024-08-25', '2024-08-24 23:46:36'),
(176, NULL, 55, 1, 'たつみ', 'いちばん', '辰巳', '一番', '1955', 1, 17, 1, 1, 420, 42, '静岡県', '静岡市葵区', '駒形通', '', 54, 54, 540, 0, 0, 0, '2026', 12, 18, '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 1, 12, 0, '2024', 8, 29, '2024', 8, 29, '0000', 0, 0, '2024-08-29', '2024-08-29 00:47:00');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '従業員ID', AUTO_INCREMENT=177;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
