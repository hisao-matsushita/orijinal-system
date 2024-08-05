-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-08-04 07:53:05
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
  `account_license_expiration_date_year` year(4) NOT NULL COMMENT '免許証有効期限（年）',
  `account_license_expiration_date_month` int(2) NOT NULL,
  `account_license_expiration_date_day` int(2) NOT NULL,
  `account_department` int(1) NOT NULL COMMENT '所属課',
  `account_classification` int(1) NOT NULL COMMENT '職種区分',
  `account_workclass` int(2) NOT NULL COMMENT '勤務区分',
  `account_employment_year` year(4) NOT NULL COMMENT '雇用年月日（年）',
  `account_employment_month` int(2) NOT NULL,
  `account_employment_day` int(2) NOT NULL,
  `registration_date` date NOT NULL DEFAULT curdate(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_password`, `account_no`, `account_salesoffice`, `account_kana01`, `account_kana02`, `account_name01`, `account_name02`, `account_birthday_year`, `account_birthday_month`, `account_birthday_day`, `account_license_expiration_date_year`, `account_license_expiration_date_month`, `account_license_expiration_date_day`, `account_department`, `account_classification`, `account_workclass`, `account_employment_year`, `account_employment_month`, `account_employment_day`, `registration_date`, `updated_at`) VALUES
(27, 't03271303', 10, 1, 'まつした', 'ひさお', '松下', '壽夫', '1983', 3, 27, '0000', 0, 0, 1, 1, 2, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(28, '', 33, 1, 'しらい', 'ゆき', '白井', '佑紀', '1979', 5, 17, '0000', 0, 0, 1, 1, 5, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(29, '', 35, 2, 'やまだ', 'たろう', '山田', '太郎', '1955', 10, 11, '0000', 0, 0, 2, 3, 8, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(44, '', 130, 1, 'もちづき', 'さとこ', '望月', '砂友子', '1982', 8, 18, '0000', 0, 0, 1, 1, 3, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(45, '', 150, 0, 'もちづき', 'さゆこ', '望月', '砂友子', '1978', 10, 15, '0000', 0, 0, 1, 1, 3, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(46, '', 330, 1, 'ほりごめ', 'あすか', '堀米', '明日香', '1983', 2, 18, '0000', 0, 0, 1, 3, 3, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(47, '', 3, 1, 'けんもち', 'ただお', '剣持', '忠雄', '1946', 5, 12, '0000', 0, 0, 1, 1, 4, '0000', 0, 0, '2024-08-02', '2024-08-02 12:34:01'),
(53, 'sdfsadfg', 66, 2, 'たつみ', 'じろう', '辰巳', '二郎', '1947', 5, 9, '0000', 0, 0, 2, 3, 12, '0000', 0, 0, '2024-08-03', '2024-08-03 12:19:39'),
(54, 'hgfhdfg', 1, 1, 'てらだ', 'ゆきひろ', '寺田', '幸廣', '1955', 2, 22, '0000', 0, 0, 1, 1, 1, '0000', 0, 0, '2024-08-03', '2024-08-03 12:20:47'),
(56, '', 77, 2, 'そぎ', 'おさむ', '曽木', '修', '1952', 2, 13, '0000', 0, 0, 2, 3, 8, '0000', 0, 0, '2024-08-04', '2024-08-03 15:29:00'),
(57, 'fgdsfg', 777, 2, 'うえだ', 'あきゆき', '上田', '商之', '1950', 7, 11, '0000', 0, 0, 1, 1, 1, '0000', 0, 0, '2024-08-04', '2024-08-03 15:46:03'),
(80, '', 15, 1, 'しみず', 'みき', '清水', '美紀', '1993', 10, 12, '2029', 8, 8, 2, 1, 8, '2024', 2, 11, '2024-08-04', '2024-08-04 03:56:22');

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
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '従業員ID', AUTO_INCREMENT=81;

--
-- テーブルの AUTO_INCREMENT `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'プライマリーキー', AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
