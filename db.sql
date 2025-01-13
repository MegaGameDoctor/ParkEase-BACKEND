-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 12 2025 г., 19:26
-- Версия сервера: 5.7.21
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `park`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--
-- Создание: Янв 07 2025 г., 18:33
-- Последнее обновление: Янв 12 2025 г., 16:07
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `f_name` varchar(255) NOT NULL,
  `l_name` varchar(255) NOT NULL,
  `companyID` int(11) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `regDate` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `companies`
--
-- Создание: Янв 07 2025 г., 09:26
-- Последнее обновление: Янв 07 2025 г., 09:45
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `descr` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `companies`
--

INSERT INTO `companies` (`id`, `name`, `code`, `descr`) VALUES
(1, 'Тест', 'abcde', 'Описание');

-- --------------------------------------------------------

--
-- Структура таблицы `parks`
--
-- Создание: Янв 08 2025 г., 09:52
-- Последнее обновление: Янв 08 2025 г., 13:53
--

DROP TABLE IF EXISTS `parks`;
CREATE TABLE `parks` (
  `id` int(11) NOT NULL,
  `companyID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `parks`
--

INSERT INTO `parks` (`id`, `companyID`, `name`) VALUES
(1, 1, 'Первая'),
(2, 1, 'Вторая');

-- --------------------------------------------------------

--
-- Структура таблицы `park_images`
--
-- Создание: Янв 08 2025 г., 09:57
-- Последнее обновление: Янв 08 2025 г., 13:53
--

DROP TABLE IF EXISTS `park_images`;
CREATE TABLE `park_images` (
  `id` int(11) NOT NULL,
  `parkID` int(11) NOT NULL,
  `url` text NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `park_images`
--

INSERT INTO `park_images` (`id`, `parkID`, `url`, `width`, `height`) VALUES
(1, 1, 'https://img.freepik.com/free-vector/top-view-car-park_1308-33210.jpg', 900, 1200),
(2, 2, 'https://fotoblik.ru/wp-content/uploads/2023/08/razmetka-parkovki-1.webp', 1500, 1800);

-- --------------------------------------------------------

--
-- Структура таблицы `park_places`
--
-- Создание: Янв 11 2025 г., 14:10
-- Последнее обновление: Янв 12 2025 г., 16:25
--

DROP TABLE IF EXISTS `park_places`;
CREATE TABLE `park_places` (
  `id` int(11) NOT NULL,
  `parkID` int(11) NOT NULL,
  `ownedBy` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `rotate` int(11) NOT NULL,
  `numb` int(11) NOT NULL,
  `car` varchar(255) NOT NULL,
  `carType` varchar(255) NOT NULL,
  `changeDate` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `park_places`
--

INSERT INTO `park_places` (`id`, `parkID`, `ownedBy`, `x`, `y`, `height`, `width`, `rotate`, `numb`, `car`, `carType`, `changeDate`) VALUES
(1, 1, 0, 246, 360, 30, 100, -180, 1, '', '', 0),
(2, 1, 0, 380, 360, 30, 100, -180, 2, '', '', 0),
(3, 1, 0, 514, 360, 30, 100, -180, 3, '', '', 0),
(4, 1, 0, 645, 360, 30, 100, -180, 4, '', '', 0),
(5, 1, 0, 246, 830, 30, 100, 0, 5, '', '', 0),
(6, 1, 0, 380, 830, 30, 100, 0, 6, '', '', 0),
(7, 1, 0, 514, 830, 30, 100, 0, 7, '', '', 0),
(8, 1, 0, 645, 830, 30, 100, 0, 8, '', '', 0),
(9, 2, 0, 400, 330, 40, 120, -70, 1, '', '', 0),
(10, 2, 0, 400, 590, 40, 120, -70, 2, '', '', 0),
(11, 2, 0, 400, 870, 40, 120, -70, 3, '', '', 0),
(12, 2, 0, 400, 1150, 40, 120, -70, 4, '', '', 0),
(13, 2, 0, 400, 1430, 40, 120, -70, 5, '', '', 0),
(14, 2, 0, 1000, 130, 40, 120, 90, 6, '', '', 0),
(15, 2, 0, 1000, 380, 40, 120, 90, 7, '', '', 0),
(16, 2, 0, 1000, 630, 40, 120, 90, 8, '', '', 0),
(17, 2, 0, 1000, 890, 40, 120, 90, 9, '', '', 0),
(18, 2, 0, 1000, 1150, 40, 120, 90, 10, '', '', 0),
(19, 2, 0, 1000, 1400, 40, 120, 90, 11, '', '', 0),
(20, 2, 0, 1000, 1660, 40, 120, 90, 12, '', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--
-- Создание: Янв 07 2025 г., 17:47
-- Последнее обновление: Янв 12 2025 г., 16:11
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `token` varchar(255) NOT NULL,
  `start` bigint(20) NOT NULL,
  `userID` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `finish` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Индексы таблицы `parks`
--
ALTER TABLE `parks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `park_images`
--
ALTER TABLE `park_images`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `park_places`
--
ALTER TABLE `park_places`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `parks`
--
ALTER TABLE `parks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `park_images`
--
ALTER TABLE `park_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `park_places`
--
ALTER TABLE `park_places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
