-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-08-21 12:22:58
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
  `account_zipcord01` char(3) NOT NULL COMMENT '郵便番号上3桁',
  `account_zipcord02` char(4) NOT NULL COMMENT '郵便番号下4桁',
  `account_pref` varchar(4) NOT NULL COMMENT '都道府県',
  `account_address01` varchar(150) NOT NULL COMMENT '市町村区',
  `account_address02` varchar(150) NOT NULL COMMENT '町名番地',
  `account_address03` varchar(100) NOT NULL COMMENT 'マンション名など',
  `account_tel01` char(4) NOT NULL COMMENT '電話番号（上）',
  `account_tel02` char(4) NOT NULL COMMENT '電話番号（中）',
  `account_tel03` char(4) NOT NULL COMMENT '電話番号（下）',
  `account_tel04` char(4) NOT NULL COMMENT '電話番号2（上）',
  `account_tel05` char(4) NOT NULL COMMENT '電話番号2（中）',
  `account_tel06` char(4) NOT NULL COMMENT '電話番号2（下）',
  `account_license_expiration_date_year` year(4) NOT NULL COMMENT '免許証有効期限（年）',
  `account_license_expiration_date_month` int(2) NOT NULL,
  `account_license_expiration_date_day` int(2) NOT NULL,
  `account_guarentor_kana01` varchar(20) NOT NULL COMMENT '保証人氏（ふりがな）',
  `account_guarentor_kana02` varchar(20) NOT NULL COMMENT '保証人名（ひらがな）',
  `account_guarentor_name01` varchar(20) NOT NULL COMMENT '保証人氏（漢字）',
  `account_guarentor_name02` varchar(20) NOT NULL COMMENT '保証人名（漢字）',
  `account_relationship` varchar(5) NOT NULL COMMENT '続柄',
  `account_guarentor_zipcord01` char(3) NOT NULL COMMENT '保証人郵便番号（上）',
  `account_guarentor_zipcord02` char(4) NOT NULL COMMENT '保証人郵便番号（下）',
  `account_guarentor_pref` varchar(4) NOT NULL COMMENT '保証人都道府県',
  `account_guarentor_address01` varchar(150) NOT NULL COMMENT '保証人市区町村',
  `account_guarentor_address02` varchar(150) NOT NULL COMMENT '保証人町名番地',
  `account_guarentor_address03` varchar(100) NOT NULL COMMENT 'マンション名など',
  `account_guarentor_tel01` char(4) NOT NULL COMMENT '保証人電話番号（上）',
  `account_guarentor_tel02` char(4) NOT NULL COMMENT '保証人電話番号（中）',
  `account_guarentor_tel03` char(4) NOT NULL COMMENT '保証人電話番号（下）',
  `account_guarentor_tel04` char(4) NOT NULL COMMENT '保証人電話番号2（上）',
  `account_guarentor_tel05` char(4) NOT NULL COMMENT '保証人電話番号2（中）',
  `account_guarentor_tel06` char(4) NOT NULL COMMENT '保証人電話番号2（下）',
  `account_department` int(1) NOT NULL COMMENT '所属課',
  `account_classification` int(1) NOT NULL COMMENT '職種区分',
  `account_workclass` int(2) NOT NULL COMMENT '勤務区分',
  `account_enrollment` int(1) NOT NULL COMMENT '在籍区分',
  `account_employment_year` year(4) NOT NULL COMMENT '雇用年月日（年）',
  `account_employment_month` int(2) NOT NULL,
  `account_employment_day` int(2) NOT NULL,
  `account_appointment_year` year(4) NOT NULL COMMENT '選任年月日（年）',
  `account_appointment_month` int(2) NOT NULL,
  `account_appointment_day` int(2) NOT NULL,
  `account_retirement_year` year(4) NOT NULL COMMENT '退職年月日（年）',
  `account_retirement_month` int(2) NOT NULL,
  `account_retirement_day` int(2) NOT NULL,
  `registration_date` date NOT NULL DEFAULT curdate(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_password`, `account_no`, `account_salesoffice`, `account_kana01`, `account_kana02`, `account_name01`, `account_name02`, `account_birthday_year`, `account_birthday_month`, `account_birthday_day`, `account_jenda`, `account_bloodtype`, `account_zipcord01`, `account_zipcord02`, `account_pref`, `account_address01`, `account_address02`, `account_address03`, `account_tel01`, `account_tel02`, `account_tel03`, `account_tel04`, `account_tel05`, `account_tel06`, `account_license_expiration_date_year`, `account_license_expiration_date_month`, `account_license_expiration_date_day`, `account_guarentor_kana01`, `account_guarentor_kana02`, `account_guarentor_name01`, `account_guarentor_name02`, `account_relationship`, `account_guarentor_zipcord01`, `account_guarentor_zipcord02`, `account_guarentor_pref`, `account_guarentor_address01`, `account_guarentor_address02`, `account_guarentor_address03`, `account_guarentor_tel01`, `account_guarentor_tel02`, `account_guarentor_tel03`, `account_guarentor_tel04`, `account_guarentor_tel05`, `account_guarentor_tel06`, `account_department`, `account_classification`, `account_workclass`, `account_enrollment`, `account_employment_year`, `account_employment_month`, `account_employment_day`, `account_appointment_year`, `account_appointment_month`, `account_appointment_day`, `account_retirement_year`, `account_retirement_month`, `account_retirement_day`, `registration_date`, `updated_at`) VALUES
(119, '$2y$10$3L3rC50..ZpBuyA9G6ElU.9Q5TkciLCkr1nz2xlAHm8S5qOZ9Rz7u', 10, 1, 'まつした', 'ひさお', '松下', '壽夫', '1983', 3, 27, 1, 2, '420', '0945', '静岡県', '静岡市葵区', '桜町2丁目6-92', '向島方', '080', '5127', '1303', '054', '5127', '1303', '2024', 10, 21, 'まつした', 'はつえ', '松下', '初恵', '母', '420', '0042', '静岡県', '静岡市葵区', '駒形通2-2-25', '辰巳マンション101号室mansyo', '054', '254', '2471', '054', '254', '5327', 1, 1, 2, 1, '2019', 6, 21, '2019', 7, 21, '0000', 0, 0, '2024-08-11', '2024-08-20 11:56:26'),
(126, '$2y$10$6G5K8EZhjxGCwFyKfPur1uav30JIo/JhscqfVlSdUnP7hpApBiKh2', 2, 1, 'しらい', 'ゆき', '白井', '佑紀', '1980', 4, 10, 2, 1, '420', '0042', '静岡県', '静岡市葵区', '駒形通', '', '0121', '0120', '1261', '1231', '156', '1651', '2024', 8, 20, 'しらい', 'みき', '白井', '美紀', '母', '420', '0042', '静岡県', '静岡市葵区', '駒形通', '', '1065', '546', '1561', '5069', '5616', '1561', 1, 1, 5, 0, '2023', 5, 21, '2023', 5, 21, '0000', 0, 0, '2024-08-12', '2024-08-21 09:40:49'),
(127, '$2y$10$pg9NbH2NlvsRDtJAxWWdfuJL3hEnPc9URJVdlfYoI3z1MmfiNqDjO', 3, 0, 'てすと', 'てすと', 'テスト', 'test', '1982', 3, 27, 1, 2, '420', '0945', '静岡県', '静岡市葵区', '桜町2丁目6-92', '向島方', '080', '5127', '1303', '054', '254', '2471', '2029', 4, 27, 'むこうじま', 'ふみえ', '向島', '文枝', '叔母', '420', '0945', '静岡県', '静岡市葵区', '桜町2丁目6-92', '', '054', '1215', '1256', '0120', '5165', '1566', 2, 3, 6, 1, '2020', 8, 10, '2020', 8, 21, '0000', 0, 0, '2024-08-12', '2024-08-16 12:14:16'),
(128, '', 773, 2, 'こんどう', 'としのぶ', '近藤', '利信', '1963', 4, 14, 1, 1, '421', '0132', '静岡県', '静岡市駿河区', '上川原22-20', 'イーグルネスト403号室', '054', '258', '8627', '090', '1985', '2230', '2029', 5, 14, 'こんどう', 'まり', '近藤', '真理', '妻', '421', '0132', '静岡県', '静岡市駿河区', '上川原22-20', 'イーグルネスト403号室', '090', '5871', '8358', '', '', '', 2, 2, 6, 1, '2024', 7, 2, '2024', 8, 0, '2000', 0, 0, '2024-08-12', '2024-08-12 09:36:10'),
(129, '$2y$10$ydvE7HvT/.igvmTn3Q9G1udcHSz0RIeNVSlzAZltIsKmNaGt4mTLy', 4, 1, 'まつした', 'ひさお', 'ログイン', 'テスト', '1983', 3, 27, 1, 1, '420', '0042', '静岡県', '静岡市葵区', '駒形通', '', '0651', '4565', '1561', '', '', '', '2031', 10, 20, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 2, 1, '2024', 1, 15, '2024', 1, 15, '0000', 0, 0, '2024-08-15', '2024-08-16 12:16:41'),
(133, '$2y$10$P17tGW5oAcsXQR59W3W9T.ZBx57WE/cJeV.2wreSau5RZf05ElgzW', 100, 1, 'しらい', 'てすと', '白井', 'テスト', '0000', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 11, 0, '2024', 8, 16, '2024', 8, 16, '0000', 0, 0, '2024-08-16', '2024-08-16 11:15:38'),
(134, '$2y$10$WUd7U1EuvVzIS.X1zkQ4oOLJVBknH4gWZbPMKqjX5/QkHvmdE7TLG', 1, 1, 'まつした', 'ひさお', '松下', '壽夫', '0000', 0, 0, 0, 0, '420', '0042', '静岡県', '静岡市葵区', '駒形通', '', '', '', '', '', '', '', '0000', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 1, 1, '2024', 8, 20, '2024', 8, 20, '0000', 0, 0, '2024-08-20', '2024-08-21 09:56:23'),
(135, '$2y$10$C/t462pNXgEFTQ91JLQt8eQ7vlmlqqUiTKZNUckJSNNbmEAstBAuG', 6, 0, 'しらい', 'ゆき', '白井', '佑紀', '0000', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 5, 1, '2024', 8, 20, '2024', 8, 20, '0000', 0, 0, '2024-08-20', '2024-08-20 11:18:51');

-- --------------------------------------------------------

--
-- テーブルの構造 `master`
--

CREATE TABLE `master` (
  `id` int(1) NOT NULL,
  `account_department` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `master`
--

INSERT INTO `master` (`id`, `account_department`) VALUES
(0, '未選択'),
(1, '内勤'),
(2, '外勤');

-- --------------------------------------------------------

--
-- テーブルの構造 `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL COMMENT 'プライマリーキー',
  `name` varchar(20) NOT NULL COMMENT '名前',
  `mail` int(10) NOT NULL COMMENT 'あいでぃ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `test`
--

INSERT INTO `test` (`id`, `name`, `mail`) VALUES
(1, '山田', 0),
(2, '佐藤', 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- テーブルのインデックス `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '従業員ID', AUTO_INCREMENT=145;

--
-- テーブルの AUTO_INCREMENT `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'プライマリーキー', AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
